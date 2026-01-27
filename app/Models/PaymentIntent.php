<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentIntent extends Model
{
    use HasFactory;

    // 'draft',        // created but not sent to provider
    //     'pending',      // sent to provider, awaiting payment
    //     'processing',   // webhook received, being processed
    //     'paid',         // payment successful
    //     'failed',       // payment failed
    //     'expired',      // timeout / cancelled
    //     'cancelled'
    protected $fillable = [
        'company_id',
        'user_id',
        'reference',
        'description',
        'amount',
        'currency',
        'payment_provider_id',
        'payment_mode_id',
        'provider_reference',

        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class);
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }
}
