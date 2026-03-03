<?php

use App\Models\Company;
use App\Models\Lang;
use App\Models\Warehouse;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;

// Get App Type
if (!function_exists('app_type')) {

    function app_type()
    {
        if (env('APP_TYPE')) {
            return env('APP_TYPE');
        } else {
            return "non-saas";
        }
    }
}

// Front Landing settings Language Key
if (!function_exists('front_lang_key')) {

    function front_lang_key()
    {
        if (session()->has('front_lang_key')) {
            return session('front_lang_key');
        }

        $globalCompanyLang = DB::table('companies')->select('lang_id')->where('is_global', 1)->first();
        $lang = $globalCompanyLang && $globalCompanyLang->lang_id && $globalCompanyLang->lang_id != null ? Lang::find($globalCompanyLang->lang_id) : Lang::first();
        session(['front_lang_key' => $lang->key]);
        return session('front_lang_key');
    }
}

// This is app setting for logged in company
if (!function_exists('company')) {

    function company($reset = false)
    {
        $request = request();
        $cacheKey = '_cached_company';

        if ($request->attributes->has($cacheKey) && $reset == false) {
            return $request->attributes->get($cacheKey);
        }

        // If it is non-saas
        if (app_type() == 'non-saas') {
            $company = Company::with(['warehouse' => function ($query) {
                return $query->withoutGlobalScope(CompanyScope::class);
            }, 'currency' => function ($query) {
                return $query->withoutGlobalScope(CompanyScope::class);
            }, 'subscriptionPlan'])->first();

            if ($company) {
                $request->attributes->set($cacheKey, $company);
                return $company;
            }

            return null;
        } else {
            $user = user();

            if ($user && $user->company_id != "") {
                $company = Company::withoutGlobalScope('company')->with(['warehouse' => function ($query) use ($user) {
                    return $query->withoutGlobalScope(CompanyScope::class)
                        ->where('company_id', $user->company_id);
                }, 'currency' => function ($query) use ($user) {
                    return $query->withoutGlobalScope(CompanyScope::class)
                        ->where('company_id', $user->company_id);
                }, 'subscriptionPlan' => function ($query) use ($user) {
                    return $query->select('id', 'name', 'modules', 'max_products', 'monthly_price', 'annual_price', 'default');
                }])->where('id', $user->company_id)->first();

                $request->attributes->set($cacheKey, $company);
                return $company;
            }

            return null;
        }
    }
}

if (!function_exists('super_admin')) {

    /**
     * Return currently logged in super admin
     */
    function super_admin()
    {
        $request = request();
        $cacheKey = '_cached_super_admin';

        if ($request->attributes->has($cacheKey)) {
            return $request->attributes->get($cacheKey);
        }

        $user = auth('api')->user();

        if ($user) {
            $request->attributes->set($cacheKey, $user);
            return $user;
        }

        return null;
    }
}

if (!function_exists('user')) {

    /**
     * Return currently logged in user with eager-loaded relations
     */
    function user($reset = false)
    {
        $request = request();
        $cacheKey = '_cached_user';

        if ($request->attributes->has($cacheKey) && $reset == false) {
            return $request->attributes->get($cacheKey);
        }

        $user = auth('api')->user();

        if ($user) {
            $user = $user->load([
                'role' => fn($query) => $query->withoutGlobalScope(CompanyScope::class),
                'role.permissions',
                'warehouse' => fn($query) => $query->withoutGlobalScope(CompanyScope::class)
                    ->where('company_id', $user->company_id),
                'activeWarehouse' => fn($query) => $query->withoutGlobalScope(CompanyScope::class),
                'userWarehouses',
            ]);

            $request->attributes->set($cacheKey, $user);
            return $user;
        }

        return null;
    }
}

if (!function_exists('warehouse')) {

    /**
     * Return the current user's active warehouse.
     * Resolution order:
     *   1. active_warehouse_id (user-selected via warehouse switch)
     *   2. warehouse_id via eager-loaded relationship
     *   3. warehouse_id via direct query (bypasses eager-load constraints)
     *   4. Company's default warehouse (last resort)
     */
    function warehouse($reset = false)
    {
        $request = request();
        $cacheKey = '_cached_warehouse';

        if ($request->attributes->has($cacheKey) && $reset == false) {
            return $request->attributes->get($cacheKey);
        }

        $user = user($reset);

        if (!$user) {
            return null;
        }

        // 1. User-selected active warehouse
        $warehouse = $user->activeWarehouse;

        // 2. Default warehouse from eager-loaded relationship
        if (!$warehouse) {
            $warehouse = $user->warehouse;
        }

        // 3. Direct query fallback (eager-load may have filtered it out via company constraint)
        if (!$warehouse && $user->warehouse_id) {
            $warehouse = Warehouse::withoutGlobalScope(CompanyScope::class)
                ->find($user->warehouse_id);
        }

        // 4. Company's default warehouse as last resort
        if (!$warehouse) {
            $company = company();
            if ($company && $company->warehouse) {
                $warehouse = $company->warehouse;
            }
        }

        if ($warehouse) {
            $request->attributes->set($cacheKey, $warehouse);
        }

        return $warehouse;
    }
}
