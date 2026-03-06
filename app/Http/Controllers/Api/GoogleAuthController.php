<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Auth\GoogleLoginRequest;
use App\Services\GoogleAuthService;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends ApiBaseController
{
    public function __construct(
        private GoogleAuthService $googleAuthService,
    ) {}

    public function login(GoogleLoginRequest $request)
    {
        try {
            $response = $this->googleAuthService->authenticate(
                $request->validated('id_token')
            );

            return ApiResponse::make('Logged in successfully', $response);
        } catch (\Examyou\RestAPI\Exceptions\ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Google OAuth: Unexpected error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Examyou\RestAPI\Exceptions\ApiException(
                'An error occurred during Google authentication. Please try again.'
            );
        }
    }
}
