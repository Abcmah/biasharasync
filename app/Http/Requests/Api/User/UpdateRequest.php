<?php

namespace App\Http\Requests\Api\User;

use App\Classes\Common;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $company = company();
        $convertedId = Hashids::decode($this->route('user'));
        $id = $convertedId[0];

        $rules = [
            'name' => 'required',
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                              ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })->ignore($id)
            ],
            'phone' => [
                'numeric',
                Rule::unique('users', 'phone')->where(function ($query) use ($company) {
                    return $query->where(function ($query) {
                        $query->where('user_type', 'staff_members')
                              ->orWhere('user_type', 'super_admins');
                    })->where('company_id', $company->id);
                })->ignore($id)
            ],
            'status' => 'required',
            'role_id' => 'required',
        ];

        // Determine warehouse requirement based on the target role
        $targetRoleName = null;
        if ($this->has('role_id') && $this->role_id != '') {
            $roleId = Common::getIdFromHash($this->role_id);
            $targetRole = Role::withoutGlobalScopes()->find($roleId);
            $targetRoleName = $targetRole ? $targetRole->name : null;
        } else {
            $editUser = User::withoutGlobalScopes()->with(['role'])->find($id);
            $targetRoleName = $editUser && $editUser->role ? $editUser->role->name : null;
        }

        if ($targetRoleName !== 'admin') {
            $rules['warehouse_id'] = 'required';
        }

        if ($this->password != '') {
            $rules['password'] = 'required|min:8';
        }

        return $rules;
    }
}
