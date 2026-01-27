<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
class PaymentProviderConfig extends BaseModel
{
    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];
    protected $fillable = [
         'company_id',
        'payment_provider_id',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
