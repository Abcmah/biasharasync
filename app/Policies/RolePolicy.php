<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Default roles (company_id = null) cannot be updated.
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->isDefault()) {
            return false;
        }

        // Company roles can only be managed by users of that company
        return $role->company_id == $user->company_id;
    }

    /**
     * Default roles (company_id = null) cannot be deleted.
     */
    public function delete(User $user, Role $role): bool
    {
        if ($role->isDefault()) {
            return false;
        }

        return $role->company_id == $user->company_id;
    }
}
