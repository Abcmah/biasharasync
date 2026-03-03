<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class CheckPermission
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth('api')->check()) {
            throw new UnauthorizedException("Unauthenticated");
        }

        $user = auth('api')->user();

        $company = $user->company_id;
        return $next($request);
        if (!$company) {
            throw new UnauthorizedException("User has no company assigned");
        }
        Log::info('dsd',[
            'dsd'=>Role::find($user->x_role_id)
        ]);
        
        // if ($user->r === 'admin') {
        //     return $next($request);
        // }

        $resourceRequests = ['index', 'store', 'update', 'show', 'destroy'];

        $routeName = $request->route()->getName();
        $urlArray = explode('.', $routeName);

        if (count($urlArray) < 3) {
            return $next($request);
        }

        $routePathString = str_replace('-', '_', $urlArray[1]);
        $resourceRequestString = $urlArray[2];

        /*
    |--------------------------------------------------------------------------
    | Special POS Check
    |--------------------------------------------------------------------------
    */
        if ($routePathString === 'pos' && !$user->isAbleTo('pos_view', $company)) {
            throw new UnauthorizedException("Don't have valid permission");
        }

        /*
    |--------------------------------------------------------------------------
    | Skip Some Routes
    |--------------------------------------------------------------------------
    */
        $skipResourcePath = ['payments'];

        if (
            in_array($resourceRequestString, $resourceRequests) &&
            !in_array($routePathString, $skipResourcePath)
        ) {

            if ($routePathString === 'langs') {
                $routePathString = "translations";
            }

            $permission = null;

            if (in_array($resourceRequestString, ['index', 'show'])) {
                $permission = $routePathString . '_view';
            }

            if ($resourceRequestString === 'store') {
                $permission = $routePathString . '_create';
            }

            if ($resourceRequestString === 'update') {
                $permission = $routePathString . '_edit';
            }

            if ($resourceRequestString === 'destroy') {
                $permission = $routePathString . '_delete';
            }

            /*
        |--------------------------------------------------------------------------
        | Final Permission Check
        |--------------------------------------------------------------------------
        */
            if ($permission && !$user->isAbleTo($permission, $company)) {
                throw new UnauthorizedException("Don't have valid permission");
            }
        }

        return $next($request);
    }
}
