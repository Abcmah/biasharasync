<?php

namespace App\Services;

use App\Events\GoogleUserAuthenticated;
use App\Models\Company;
use App\Models\User;
use Examyou\RestAPI\Exceptions\ApiException;
use Google\Auth\AccessToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    private AccessToken $accessToken;

    public function __construct()
    {
        $this->accessToken = new AccessToken();
    }

    public function authenticate(string $idToken): array
    {
        $payload = $this->verifyToken($idToken);
        $user = $this->findOrCreateUser($payload);

        // New users without a company need onboarding — don't validate company access
        if (!$user->company_id) {
            $token = auth('api')->login($user);
            return $this->buildOnboardingResponse($token, $user);
        }

        $this->validateUserAccess($user);

        $token = auth('api')->login($user);

        return $this->buildResponse($token, $user);
    }

    private function verifyToken(string $idToken): array
    {
        $clientId = config('services.google.client_id');

        if (empty($clientId)) {
            Log::error('Google OAuth: GOOGLE_CLIENT_ID not configured');
            throw new ApiException('Google authentication is not configured.', null, 500);
        }

        try {
            $payload = $this->accessToken->verify($idToken, [
                'certsLocation' => AccessToken::IAP_CERT_URL,
                'throwException' => true,
            ]);
        } catch (\Exception $e) {
            // Fallback: verify using Google's standard token info endpoint
            $payload = $this->verifyViaTokenEndpoint($idToken);
        }

        if (empty($payload)) {
            Log::warning('Google OAuth: Invalid or expired token');
            throw new ApiException('Invalid or expired Google token.', null, 401);
        }

        // Verify audience matches our client ID
        if (!isset($payload['aud']) || $payload['aud'] !== $clientId) {
            Log::warning('Google OAuth: Audience mismatch', [
                'expected' => $clientId,
                'received' => $payload['aud'] ?? 'missing',
            ]);
            throw new ApiException('Invalid Google token audience.', null, 401);
        }

        // Verify issuer
        $validIssuers = ['accounts.google.com', 'https://accounts.google.com'];
        if (!isset($payload['iss']) || !in_array($payload['iss'], $validIssuers)) {
            Log::warning('Google OAuth: Invalid issuer', ['iss' => $payload['iss'] ?? 'missing']);
            throw new ApiException('Invalid Google token issuer.', null, 401);
        }

        // Verify expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new ApiException('Google token has expired.', null, 401);
        }

        // Ensure email is present
        if (empty($payload['email'])) {
            throw new ApiException('Email not provided by Google. Please ensure email access is granted.', null, 422);
        }

        return $payload;
    }

    private function verifyViaTokenEndpoint(string $idToken): ?array
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 5]);
            $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query' => ['id_token' => $idToken],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::warning('Google OAuth: Token endpoint verification failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function findOrCreateUser(array $payload): User
    {
        $email = strtolower($payload['email']);
        $googleId = $payload['sub'];
        $name = $payload['name'] ?? explode('@', $email)[0];

        return DB::transaction(function () use ($email, $googleId, $name) {
            // First: try to find by google_id (most reliable)
            $user = User::where('google_id', $googleId)
                ->where('user_type', 'staff_members')
                ->first();

            if ($user) {
                Log::info('Google OAuth: Existing user login via google_id', ['user_id' => $user->id]);
                return $user;
            }

            // Second: try to find by email
            $user = User::where('email', $email)
                ->where('user_type', 'staff_members')
                ->first();

            if ($user) {
                // Link Google account to existing user
                $user->google_id = $googleId;

                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                $user->save();

                Log::info('Google OAuth: Linked Google account to existing user', ['user_id' => $user->id]);
                GoogleUserAuthenticated::dispatch($user, false);

                return $user;
            }

            // Third: create new user
            $user = $this->createNewUser($email, $googleId, $name);

            Log::info('Google OAuth: New user created', ['user_id' => $user->id]);
            GoogleUserAuthenticated::dispatch($user, true);

            return $user;
        });
    }

    private function createNewUser(string $email, string $googleId, string $name): User
    {
        // Security: Do NOT assign to any existing company.
        // User will go through the onboarding flow to create their own company.
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->google_id = $googleId;
        $user->user_type = 'staff_members';
        $user->email_verified_at = now();
        $user->status = 'enabled';
        $user->login_enabled = true;
        $user->is_superadmin = 0;
        // company_id, role_id, warehouse_id intentionally left null
        $user->save();

        return $user;
    }

    private function buildOnboardingResponse(string $token, User $user): array
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => \Carbon\Carbon::now()->addDays(180),
            'user' => [
                'xid' => $user->xid,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image_url' => $user->profile_image_url,
                'user_type' => $user->user_type,
                'is_superadmin' => 0,
                'role' => null,
                'warehouse' => null,
            ],
            'needs_onboarding' => true,
        ];
    }

    private function validateUserAccess(User $user): void
    {
        $company = Company::find($user->company_id);

        if (!$company || $company->status === 'pending') {
            throw new ApiException('Your company is not verified.');
        }

        if ($company->status === 'inactive') {
            throw new ApiException('Company account deactivated.');
        }

        if ($user->status === 'waiting') {
            throw new ApiException('User not verified.');
        }

        if ($user->status === 'disabled') {
            throw new ApiException('Account deactivated.');
        }

        if (!$user->login_enabled) {
            throw new ApiException('Login is disabled for this account.');
        }
    }

    private function buildResponse(string $token, User $user): array
    {
        $user->load([
            'role' => fn($query) => $query->withoutGlobalScopes(),
            'role.permissions',
            'userWarehouses',
            'defaultWarehouse' => fn($query) => $query->withoutGlobalScopes(),
            'activeWarehouse' => fn($query) => $query->withoutGlobalScopes(),
        ]);

        // Ensure role_user pivot is in sync
        if ($user->role_id && $user->id) {
            $pivotExists = DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $user->role_id)
                ->exists();

            if (!$pivotExists) {
                $user->syncRoles([$user->role_id]);
            }
        }

        $userCompany = Company::find($user->company_id);
        $addMenuSetting = \App\Models\Settings::where('setting_type', 'shortcut_menus')->first();

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => \Carbon\Carbon::now()->addDays(180),
            'user' => new \App\Http\Resources\UserResource($user),
            'app' => $userCompany,
            'shortcut_menus' => $addMenuSetting,
            'email_setting_verified' => \App\Models\Settings::where('setting_type', 'email')
                ->where('status', 1)
                ->where('verified', 1)
                ->count() > 0 ? 1 : 0,
            'visible_subscription_modules' => \App\Classes\Common::allVisibleSubscriptionModules(),
        ];
    }
}
