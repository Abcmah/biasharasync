<?php

namespace App\Services;

use App\Models\PaymentProvider;
use Illuminate\Support\Facades\Cache;

class PaymentProviderService
{
    protected function cacheKey($company_id)
    {
        return "company_{$company_id}";
    }
    public function get($company_id, $provider = 'mpesa')
    {
        return Cache::remember(
            $this->cacheKey($company_id),
            now()->addHours(12),
            function () use ($company_id, $provider) {
                return PaymentProvider::with('config')->where('company_id', $company_id)->where('code', $provider)->first();
            }
        );
    }

    public function clear($company_id)
    {
        Cache::forget($this->cacheKey($company_id));
    }
}
