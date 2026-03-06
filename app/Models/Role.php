<?php

namespace App\Models;

use App\Classes\Common;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Laratrust\Checkers\CheckersManager;
use Laratrust\Checkers\Role\RoleChecker;
use Laratrust\Contracts\Role as RoleContract;
use Laratrust\Helper;
use Laratrust\Traits\DynamicUserRelationshipCalls;
use Laratrust\Traits\HasLaratrustEvents;
use Ramsey\Uuid\UuidInterface;

class Role extends BaseModel implements RoleContract
{
    use HasLaratrustEvents;
    use DynamicUserRelationshipCalls;

    protected $table = 'roles';

    protected $default = ['xid', 'id', 'name', 'display_name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = ['xid'];

    protected static function boot()
    {
        parent::boot();

        // Custom scope: show company-owned roles AND default roles (company_id = null)
        static::addGlobalScope('company_with_defaults', function (Builder $builder) {
            if (auth('api')->check() && Schema::hasColumn('roles', 'company_id')) {
                $company = company();
                if ($company) {
                    $companyId = Common::getIdFromHash($company->xid);
                    $builder->where(function ($query) use ($companyId) {
                        $query->where('roles.company_id', $companyId)
                              ->orWhereNull('roles.company_id');
                    });
                }
            }
        });
    }

    protected static function booted(): void
    {
        $flushCache = function (Role $role) {
            $role->flushCache();
        };

        if (method_exists(static::class, 'restored')) {
            static::restored($flushCache);
        }

        static::deleted($flushCache);
        static::saved($flushCache);

        static::deleting(function ($role) {
            if (method_exists($role, 'bootSoftDeletes') && ! $role->forceDeleting) {
                return;
            }

            $role->permissions()->sync([]);

            foreach (array_keys(Config::get('laratrust.user_models')) as $key) {
                $role->$key()->sync([]);
            }
        });
    }

    protected function laratrustRoleChecker(): RoleChecker
    {
        return (new CheckersManager($this))->getRoleChecker();
    }

    public function getMorphByUserRelation(string $relationship): MorphToMany
    {
        return $this->morphedByMany(
            Config::get('laratrust.user_models')[$relationship],
            'user',
            Config::get('laratrust.tables.role_user'),
            Config::get('laratrust.foreign_keys.role'),
            Config::get('laratrust.foreign_keys.user')
        );
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Config::get('laratrust.models.permission'),
            Config::get('laratrust.tables.permission_role'),
            Config::get('laratrust.foreign_keys.role'),
            Config::get('laratrust.foreign_keys.permission')
        );
    }

    public function hasPermission(string|array|BackedEnum $permission, bool $requireAll = false): bool
    {
        return $this->laratrustRoleChecker()
            ->currentRoleHasPermission($permission, $requireAll);
    }

    public function syncPermissions(iterable $permissions): static
    {
        $mappedPermissions = [];

        foreach ($permissions as $permission) {
            $mappedPermissions[] = Helper::getIdFor($permission, 'permission');
        }

        $changes = $this->permissions()->sync($mappedPermissions);
        $this->flushCache();
        $this->fireLaratrustEvent('permission.synced', [$this, $changes]);

        return $this;
    }

    public function givePermission(array|string|int|Model|UuidInterface|BackedEnum $permission): static
    {
        $permission = Helper::getIdFor($permission, 'permission');

        $this->permissions()->attach($permission);
        $this->flushCache();
        $this->fireLaratrustEvent('permission.added', [$this, $permission]);

        return $this;
    }

    public function removePermission(array|string|int|Model|UuidInterface|BackedEnum $permission): static
    {
        $permission = Helper::getIdFor($permission, 'permission');

        $this->permissions()->detach($permission);
        $this->flushCache();
        $this->fireLaratrustEvent('permission.removed', [$this, $permission]);

        return $this;
    }

    public function givePermissions(iterable $permissions): static
    {
        foreach ($permissions as $permission) {
            $this->givePermission($permission);
        }

        return $this;
    }

    public function removePermissions(?iterable $permissions = null): static
    {
        if (! $permissions) {
            $this->syncPermissions([]);

            return $this;
        }

        foreach ($permissions as $permission) {
            $this->removePermission($permission);
        }

        return $this;
    }

    public function flushCache(): void
    {
        $this->laratrustRoleChecker()->currentRoleFlushCache();
    }

    /**
     * Default roles (company_id = null) are system-wide and cannot be modified or deleted.
     */
    public function isDefault(): bool
    {
        return is_null($this->company_id);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function toArray()
    {
        $array = parent::toArray();

        if (array_key_exists('permissions', $array)) {
            $array['perms'] = $array['permissions'];
            unset($array['permissions']);
        }

        return $array;
    }
}
