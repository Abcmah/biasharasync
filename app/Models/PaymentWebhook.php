<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhook extends Model
{
    // 'received',
    //     'processed',
    //     'failed',
    //     'ignored',
    //     'replayed',
     protected $fillable = [
        'company_id',
        'payment_provider_id',
        'payment_id',
        'provider_event',
        'provider_reference',
        'signature',
        'http_method',
        'endpoint',
        'ip_address',
        'http_status',
        'headers',
        'payload',
        'response',
        'status',
        'error_message',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'response' => 'array',
    ];

    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
