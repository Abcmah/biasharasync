<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $role = $this->role;

        return [
            'xid' => $this->xid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_image_url' => $this->profile_image_url,
            'user_type' => $this->user_type,
            'is_superadmin' => $this->is_superadmin,
            'role' => $role ? [
                'xid' => $role->xid,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'perms' => $role->relationLoaded('permissions')
                    ? $role->permissions->map(fn($p) => ['xid' => $p->xid, 'name' => $p->name])
                    : [],
            ] : null,
            'warehouse' => $this->activeWarehouse ?? $this->defaultWarehouse,
        ];
    }
}
