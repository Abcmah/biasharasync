<?php

namespace App\Services;

use App\Classes\Common;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\Role;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OnboardingService
{
    public function createCompanyForUser(User $user, array $data): array
    {
        $planId = Common::getIdFromHash($data['subscription_plan_id']);
        $plan = SubscriptionPlan::find($planId);

        if (!$plan) {
            throw new ApiException('Invalid subscription plan.', null, 422);
        }

        return DB::transaction(function () use ($user, $data, $plan) {
            // Lock user row to prevent race condition (duplicate company creation)
            $user = User::lockForUpdate()->find($user->id);

            if ($user->company_id) {
                throw new ApiException('Company already created.', null, 409);
            }

            // 1. Create company
            $company = new Company();
            $company->name = $data['name'];
            $company->email = $data['email'] ?? $user->email;
            $company->phone = $data['phone'] ?? null;
            $company->address = $data['address'] ?? null;
            $company->timezone = $data['timezone'] ?? 'Africa/Nairobi';
            $company->status = 'active';
            $company->verified = 1;
            $company->is_global = 0;
            $company->save();

            // Set guarded fields via DB update
            DB::table('companies')->where('id', $company->id)->update([
                'subscription_plan_id' => $plan->id,
                'admin_id' => $user->id,
                'licence_expire_on' => Carbon::now()->addDays($plan->duration ?: 30),
            ]);

            // 2. Create default warehouse
            $warehouse = new Warehouse();
            $warehouse->name = $data['name'] . ' Warehouse';
            $warehouse->slug = Str::slug($data['name']) . '-warehouse';
            $warehouse->email = $company->email;
            $warehouse->phone = $company->phone;
            $warehouse->save();

            // Set guarded company_id on warehouse
            DB::table('warehouses')->where('id', $warehouse->id)->update([
                'company_id' => $company->id,
            ]);

            // Link warehouse to company
            DB::table('companies')->where('id', $company->id)->update([
                'warehouse_id' => $warehouse->id,
            ]);

            // 3. Find the default admin role
            $adminRole = Role::withoutGlobalScopes()
                ->where('name', 'admin')
                ->where(function ($q) use ($company) {
                    $q->where('company_id', $company->id)
                        ->orWhereNull('company_id');
                })
                ->first();

            // 4. Assign user to company as admin
            User::where('id', $user->id)->update([
                'company_id' => $company->id,
                'role_id' => $adminRole ? $adminRole->id : null,
                'warehouse_id' => $warehouse->id,
            ]);

            $user->refresh();

            // 5. Sync Laratrust role pivot
            if ($adminRole) {
                $user->syncRoles([$adminRole->id]);
            }

            // 6. Link user to warehouse
            UserWarehouse::create([
                'user_id' => $user->id,
                'warehouse_id' => $warehouse->id,
            ]);

            Log::info('Onboarding: Company created', [
                'user_id' => $user->id,
                'company_id' => $company->id,
            ]);

            // 7. Return full login response
            $company->refresh();

            return $this->buildFullResponse($user, $company);
        });
    }

    private function buildFullResponse(User $user, Company $company): array
    {
        $user->load([
            'role' => fn($query) => $query->withoutGlobalScopes(),
            'role.permissions',
            'userWarehouses',
            'defaultWarehouse' => fn($query) => $query->withoutGlobalScopes(),
            'activeWarehouse' => fn($query) => $query->withoutGlobalScopes(),
        ]);

        $addMenuSetting = Settings::withoutGlobalScopes()
            ->where('setting_type', 'shortcut_menus')
            ->where('company_id', $company->id)
            ->first();

        return [
            'user' => new UserResource($user),
            'app' => $company,
            'shortcut_menus' => $addMenuSetting,
            'email_setting_verified' => 0,
            'visible_subscription_modules' => Common::allVisibleSubscriptionModules(),
        ];
    }
}
