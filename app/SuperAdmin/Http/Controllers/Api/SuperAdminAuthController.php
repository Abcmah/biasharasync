<?php

namespace App\SuperAdmin\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\Api\AuthController;
use App\Models\Company;
use App\Models\Currency;
use App\Models\PaymentMode;
use App\Models\Settings;
use App\Models\Warehouse;
use App\Scopes\CompanyScope;
use App\SuperAdmin\Http\Requests\Api\Auth\LoginRequest;
use App\SuperAdmin\Models\GlobalCompany;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class SuperAdminAuthController extends AuthController
{
    public function globalSetting()
    {
        $settings = GlobalCompany::first();

        return ApiResponse::make('Success', [
            'global_setting' => $settings,
        ]);
    }

    public function appDetails()
    {
        $company = company(true);
        $company = $company ? $company : GlobalCompany::first();
        $addMenuSetting = $company ? Settings::where('setting_type', 'shortcut_menus')->first() : null;
        $totalCurrencies = Currency::withoutGlobalScope(CompanyScope::class)
            ->where('currencies.company_id', $company->id)->count();
        $totalPaymentModes = PaymentMode::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)->count();
        $totalWarehouses = Warehouse::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)->count();

        return ApiResponse::make('Success', [
            'app' => $company,
            'shortcut_menus' => $addMenuSetting,
            'email_setting_verified' => $this->emailSettingVerified(),
            'total_currencies' => $totalCurrencies,
            'total_warehouses' => $totalWarehouses,
            'total_payment_modes' => $totalPaymentModes
        ]);
    }

    public function superAdminLogin(LoginRequest $request)
    {
        $credentials = [
            'password' => $request->password,
        ];

        if (is_numeric($request->email)) {
            $credentials['phone'] = $request->email;
        } else {
            $credentials['email'] = $request->email;
        }

        $token = auth('api')->attempt($credentials);

        if (!$token) {
            throw new ApiException('These credentials do not match our records.');
        }

        $user = auth('api')->user();

        if ($user->user_type === 'super_admins') {
            $userCompany = GlobalCompany::find($user->company_id);
        } else {
            $userCompany = Company::find($user->company_id);
        }

        if (!$userCompany || $userCompany->status === 'pending') {
            throw new ApiException('Your company not verified.');
        } elseif ($userCompany->status === 'inactive') {
            throw new ApiException('Company account deactivated.');
        } elseif ($user->status === 'waiting') {
            throw new ApiException('User not verified.');
        } elseif ($user->status === 'disabled') {
            throw new ApiException('Account deactivated.');
        }

        $response = $this->respondWithToken($token, $user);

        $addMenuSetting = Settings::where('setting_type', 'shortcut_menus')->first();

        $response['app'] = $userCompany;
        $response['shortcut_menus'] = $addMenuSetting;
        $response['email_setting_verified'] = $this->emailSettingVerified();
        $response['visible_subscription_modules'] = Common::allVisibleSubscriptionModules();

        return ApiResponse::make('Logged in successfully', $response);
    }
}
