<?php

namespace App\Traits;

use App\Classes\Common;
use App\Classes\Notify;
use App\Http\Requests\Api\Customer\ImportRequest;
use App\Imports\UserImport;
use App\Models\Role;
use App\Models\UserWarehouse;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

trait UserTraits
{
    public $userType = "";

    public function modifyIndex($query)
    {
        $warehouse = warehouse();

        $query = $query->leftJoin('user_warehouse', 'users.id', '=', 'user_warehouse.user_id')
                       ->join('roles', 'roles.id', '=', 'users.role_id');

        if ($this->userType == 'staff_members') {
            $user = user();
            $query = $query->where('users.company_id', $user->company_id);
        }

        // Show users assigned to current warehouse, plus users whose role
        // grants full access (determined by having no warehouse restriction).
        $query = $query->where(function ($qury) use ($warehouse) {
            $qury->where('user_warehouse.warehouse_id', $warehouse->id)
                 ->orWhereNotExists(function ($sub) {
                     $sub->select(DB::raw(1))
                         ->from('user_warehouse')
                         ->whereColumn('user_warehouse.user_id', 'users.id');
                 });
        });

        $query = $query->where('users.user_type', $this->userType);

        return $query;
    }

    public function storing($user)
    {
        $loggedUser = user();
        $request = request();
        $warehouse = warehouse();
        $company = company();

        if ($user->user_type != $this->userType) {
            throw new ApiException("Don't have valid permission");
        }

        if ($user->user_type == 'staff_members') {
            // Role is required — validated via StoreRequest, decoded here
            if (!$request->has('role_id') || !$request->role_id) {
                throw new ApiException("Role is required for staff members");
            }

            $roleId = $this->getIdFromHash($request->role_id);
            $this->validateRoleBelongsToCompany($roleId, $company);
            $user->role_id = $roleId;
        }

        $user->warehouse_id = $this->resolveWarehouseId($loggedUser, $request, $warehouse);
        $user->created_by = $loggedUser->id;
        $user->lang_id = $company->lang_id;

        return $user;
    }

    public function stored($user)
    {
        $this->syncRoleAndWarehouses($user);

        Notify::send('staff_member_create', $user);

        Common::calculateTotalUsers($user->company_id, true);
    }

    public function updating($user)
    {
        $loggedUser = user();
        $request = request();

        if ($user->user_type != $this->userType) {
            throw new ApiException("Don't have valid permission");
        }

        // Prevent users from changing their own role
        if ($loggedUser->id == $user->id && $user->isDirty('role_id')) {
            throw new ApiException("You can not change your role.");
        }

        // If this user currently has admin role, ensure they're not the last admin
        if ($user->user_type == 'staff_members' && $user->isDirty('role_id')) {
            $this->ensureNotLastAdmin($user);
        }

        // Non-admin users cannot update staff from other warehouses
        if (!$this->loggedUserIsAdmin($loggedUser) && $user->user_type == 'staff_members') {
            if ($loggedUser->warehouse_id != $user->warehouse_id) {
                throw new ApiException("Don't have valid permission");
            }
        }

        // Demo mode protection
        if (env('APP_ENV') == 'production' && ($user->isDirty('password') || $user->isDirty('email') || $user->isDirty('status') || $user->isDirty('role_id'))) {
            if ($user->user_type == 'staff_members' && in_array($user->getOriginal('email'), ['admin@example.com', 'stockmanager@example.com', 'salesman@example.com'])) {
                throw new ApiException('Not Allowed In Demo Mode');
            }
        }

        if ($user->user_type == 'staff_members') {
            if ($request->has('role_id') && $request->role_id) {
                $roleId = $this->getIdFromHash($request->role_id);
                $this->validateRoleBelongsToCompany($roleId, company());
                $user->role_id = $roleId;
            } else {
                // Keep existing role if not provided
                $user->role_id = $user->getOriginal('role_id');
            }
        }

        if ($this->loggedUserIsAdmin($loggedUser)) {
            $user->warehouse_id = $request->warehouse_id;
        }

        return $user;
    }

    public function updated($user)
    {
        $this->syncRoleAndWarehouses($user);

        Notify::send('staff_member_update', $user);
    }

    /**
     * Sync the Laratrust role_user pivot and warehouse assignments for a staff member.
     */
    public function syncRoleAndWarehouses($user)
    {
        $request = request();

        if ($user->user_type == 'staff_members' && $user->role_id) {
            // Use Laratrust syncRoles to replace any existing pivot entries
            $user->syncRoles([$user->role_id]);
        }

        // Sync warehouse assignments
        UserWarehouse::where('user_id', $user->id)->delete();
        if ($request->has('warehouses')) {
            foreach ($request->warehouses as $selectedWarehouse) {
                $selectedWarehouseId = Common::getIdFromHash($selectedWarehouse);

                $userWarehouse = new UserWarehouse();
                $userWarehouse->user_id = $user->id;
                $userWarehouse->warehouse_id = $selectedWarehouseId;
                $userWarehouse->save();
            }
        }

        return $user;
    }

    public function destroying($user)
    {
        if ($user->user_type != $this->userType) {
            throw new ApiException("Don't have valid permission");
        }

        $loggedUser = user();
        $loggedUserCompany = company();

        if ($loggedUserCompany->admin_id == $user->id) {
            throw new ApiException('Can not delete company root admin');
        }

        if (env('APP_ENV') == 'production' && $user->user_type == 'staff_members' && in_array($user->getOriginal('email'), ['admin@example.com', 'stockmanager@example.com', 'salesman@example.com'])) {
            throw new ApiException('Not Allowed In Demo Mode');
        }

        // Prevent deleting the last admin
        if ($user->user_type == 'staff_members' && $user->role_id) {
            $this->ensureNotLastAdmin($user, true);
        }

        if (!$this->loggedUserIsAdmin($loggedUser) && $user->user_type == 'staff_members') {
            if ($loggedUser->warehouse_id != $user->warehouse_id) {
                throw new ApiException("Don't have valid permission");
            }
        }

        if ($loggedUser->id == $user->id) {
            throw new ApiException('Can not delete yourself.');
        }

        return $user;
    }

    public function destroyed($user)
    {
        // Clean up Laratrust pivot entries
        $user->syncRoles([]);

        Common::calculateTotalUsers($user->company_id, true);

        Notify::send('staff_member_delete', $user);
    }

    public function import(ImportRequest $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new UserImport($this->userType), request()->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    /**
     * Validate that the given role ID belongs to the user's company or is a default role.
     */
    private function validateRoleBelongsToCompany(int $roleId, $company): void
    {
        $role = Role::withoutGlobalScopes()->find($roleId);

        if (!$role) {
            throw new ApiException("The selected role does not exist.");
        }

        // Role must be either a default role (company_id = null) or belong to this company
        if ($role->company_id !== null && $role->company_id != $company->id) {
            throw new ApiException("The selected role does not belong to your company.");
        }
    }

    /**
     * Check if logged user has admin role, using the role_id column as the reliable source.
     */
    private function loggedUserIsAdmin($loggedUser): bool
    {
        if (!$loggedUser || !$loggedUser->role) {
            return false;
        }

        return $loggedUser->role->name === 'admin';
    }

    /**
     * Ensure the user being modified is not the last admin.
     * @param bool $deleting Whether we're deleting (checks differently than role change)
     */
    private function ensureNotLastAdmin($user, bool $deleting = false): void
    {
        $currentRole = Role::withoutGlobalScopes()->find($user->getOriginal('role_id'));

        if (!$currentRole || $currentRole->name !== 'admin') {
            return;
        }

        // Count admins via the role_user pivot (Laratrust) within this company
        $adminRoleCount = DB::table('role_user')
            ->where('role_id', $currentRole->id)
            ->count();

        if ($adminRoleCount <= 1) {
            $message = $deleting
                ? 'You are the only admin. Cannot delete this user.'
                : 'Cannot change role — this is the only admin.';
            throw new ApiException($message);
        }
    }

    /**
     * Resolve which warehouse_id to assign based on the logged user's role.
     */
    private function resolveWarehouseId($loggedUser, $request, $warehouse)
    {
        if ($this->loggedUserIsAdmin($loggedUser)) {
            return $request->warehouse_id;
        }

        return $warehouse->id;
    }
}
