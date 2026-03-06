<?php

namespace App\Http\Requests\Api\Role;

use App\Models\Role;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class DeleteRequest extends FormRequest
{
    public function authorize()
    {
        $convertedId = Hashids::decode($this->route('role'));
        if (empty($convertedId)) {
            return false;
        }

        $role = Role::withoutGlobalScopes()->find($convertedId[0]);

        // Reject deletion of default system roles at the request level
        if ($role && $role->isDefault()) {
            throw new ApiException('Default system roles cannot be deleted.');
        }

        return true;
    }

    public function rules()
    {
        return [];
    }
}
