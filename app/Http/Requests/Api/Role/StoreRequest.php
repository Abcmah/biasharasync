<?php

namespace App\Http\Requests\Api\Role;

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

        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })
            ],
            'display_name' => 'required',
            'permissions' => 'required',
        ];
    }
}
