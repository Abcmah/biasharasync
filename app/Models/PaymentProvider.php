<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentProvider extends BaseModel
{
    use HasFactory;

    protected $default = ['xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $filterable = ['name'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $fillable = [
        'company_id',
        'payment_mode_id',
        'name',
        'code',
        'display_name',
        'mode_type',
        'priority',
        'is_active',
    ];

    public function config()
    {
        return $this->hasOne(PaymentProviderConfig::class);
    }


    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
