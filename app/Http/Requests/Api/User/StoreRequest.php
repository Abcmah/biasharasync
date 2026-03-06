<?php

namespace App\Http\Requests\Api\User;

use App\Classes\Common;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $company = company();
        $loggedUser = auth('api')->user();

        $rules = [
            'name' => 'required',
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                              ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })
            ],
            'phone' => [
                'numeric',
                Rule::unique('users', 'phone')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                              ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })
            ],
            'status' => 'required',
            'password' => 'required|min:8',
            // Role is always required for staff members
            'role_id' => 'required',
        ];

        // Require warehouse unless the selected role is one that doesn't need it
        if ($this->has('role_id') && $this->role_id != '') {
            $roleId = Common::getIdFromHash($this->role_id);
            $role = Role::withoutGlobalScopes()->find($roleId);

            if ($role && $role->name !== 'admin') {
                $rules['warehouse_id'] = 'required';
            }
        } else {
            $rules['warehouse_id'] = 'required';
        }

        return $rules;
    }
}
