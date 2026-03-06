<?php

namespace App\Http\Requests\Api\Role;

use App\Models\Role;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        $convertedId = Hashids::decode($this->route('role'));
        if (empty($convertedId)) {
            return false;
        }

        $role = Role::withoutGlobalScopes()->find($convertedId[0]);

        // Reject updates to default system roles at the request level
        if ($role && $role->isDefault()) {
            throw new ApiException('Default system roles cannot be edited.');
        }

        return true;
    }

    public function rules()
    {
        $company = company();
        $convertedId = Hashids::decode($this->route('role'));
        $id = $convertedId[0];

        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->where(function ($query) use ($company, $id) {
                    return $query->where('company_id', $company->id)
                                ->where('id', '!=', $id);
                })
            ],
            'display_name' => 'required',
            'permissions' => 'required',
        ];
    }
}
