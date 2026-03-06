<?php

namespace App\Http\Middleware;

use Closure;
use Examyou\RestAPI\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Resolves the user's role via the role_id column (reliable source of truth),
     * then checks the role's permissions for the requested resource action.
     */
    public function handle($request, Closure $next)
    {
        if (!auth('api')->check()) {
            throw new UnauthorizedException("Unauthenticated");
        }

        $user = auth('api')->user();

        if (!$user->company_id) {
            throw new UnauthorizedException("User has no company assigned");
        }

        // Ensure the user has a role loaded (via role_id column)
        $this->ensureRoleLoaded($user);

        // Admin role has full access
        if ($user->role && $user->role->name === 'admin') {
            return $next($request);
        }

        $resourceRequests = ['index', 'store', 'update', 'show', 'destroy'];

        $routeName = $request->route()->getName();
        if (!$routeName) {
            return $next($request);
        }

        $urlArray = explode('.', $routeName);

        if (count($urlArray) < 3) {
            return $next($request);
        }

        $routePathString = str_replace('-', '_', $urlArray[1]);
        $resourceRequestString = $urlArray[2];

        // Special POS permission check
        if ($routePathString === 'pos' && !$this->userHasPermission($user, 'pos_view')) {
            throw new UnauthorizedException("Don't have valid permission");
        }

        // Routes that skip permission checks
        $skipResourcePath = ['payments'];

        if (
            in_array($resourceRequestString, $resourceRequests) &&
            !in_array($routePathString, $skipResourcePath)
        ) {
            if ($routePathString === 'langs') {
                $routePathString = 'translations';
            }

            $permission = null;

            if (in_array($resourceRequestString, ['index', 'show'])) {
                $permission = $routePathString . '_view';
            } elseif ($resourceRequestString === 'store') {
                $permission = $routePathString . '_create';
            } elseif ($resourceRequestString === 'update') {
                $permission = $routePathString . '_edit';
            } elseif ($resourceRequestString === 'destroy') {
                $permission = $routePathString . '_delete';
            }

            if ($permission && !$this->userHasPermission($user, $permission)) {
                throw new UnauthorizedException("Don't have valid permission");
            }
        }

        return $next($request);
    }

    /**
     * Ensure the user's role relationship is loaded.
     * Falls back to role_user pivot if role_id is not set.
     */
    private function ensureRoleLoaded($user): void
    {
        if ($user->relationLoaded('role') && $user->role) {
            return;
        }

        $user->load([
            'role' => fn($q) => $q->withoutGlobalScope(CompanyScope::class)
                                  ->withoutGlobalScope('company_with_defaults'),
            'role.permissions',
        ]);

        // Fallback: resolve role from role_user pivot if role_id column is empty
        if (!$user->role && $user->id) {
            $roleId = DB::table('role_user')
                ->where('user_id', $user->id)
                ->value('role_id');

            if ($roleId) {
                $user->role_id = $roleId;
                $user->save();
                $user->load([
                    'role' => fn($q) => $q->withoutGlobalScope(CompanyScope::class)
                                          ->withoutGlobalScope('company_with_defaults'),
                    'role.permissions',
                ]);
            }
        }
    }

    /**
     * Check if the user's role has a specific permission.
     */
    private function userHasPermission($user, string $permissionName): bool
    {
        if (!$user->role || !$user->role->relationLoaded('permissions')) {
            return false;
        }

        return $user->role->permissions->contains('name', $permissionName);
    }
}
