<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MpesaService
{
    private $company;
    private $config;
    private $mpesaURL;
    private $consumerKey;
    private $consumerSecret;
    private $passkey;
    private $transactionType;
    private $businessShortCode;
    private $callBackURL;
    public function __construct(int $company, $to_biotrack = false)
    {

        if (!$to_biotrack) {
            $this->company = $company;
            // app(PaymentProviderService::class)->clear($company);
            $paymentProvider = app(PaymentProviderService::class)->get($company);

            $config = $paymentProvider->config->config ?? [];

            $env = $paymentProvider->env ?? null;
            $this->transactionType = $config['transaction_type'] ?? null;
            $this->businessShortCode = $config['shortcode'] ?? null;
            $this->consumerKey    = $config['consumer_key'] ?? null;
            $this->consumerSecret = $config['consumer_secret'] ?? null;
            $this->passkey        = $config['passkey'] ?? null;
            $this->callBackURL    = $config['callback_url'] ?? null;

            $this->mpesaURL = $env === 'sandbox'
                ? 'https://sandbox.safaricom.co.ke'
                : 'https://api.safaricom.co.ke';

            if (!$this->consumerKey || !$this->consumerSecret) {
                throw new \Exception("Mpesa configuration is incomplete for church ID {$company}");
            }
        } else {

            $this->mpesaURL = config('services.mpesa.environment') === 'sandbox'
                ? 'https://sandbox.safaricom.co.ke'
                : 'https://api.safaricom.co.ke';
            $this->consumerKey = config('services.mpesa.consumer_key');
            $this->consumerSecret = config('services.mpesa.consumer_secret');
            $this->passkey  = config('services.mpesa.passkey');
            $this->transactionType =config('services.mpesa.transaction_type');
            $this->businessShortCode = config('services.mpesa.shortcode');
            $this->callBackURL = config('services.mpesa.callback_url');

        }


    }

    public function getAccessToken(): mixed
    {
        // return Cache::remember('mpesa_access_token', 300, function () {
        $response = Http::withBasicAuth(
            $this->consumerKey,
            $this->consumerSecret
        )->get("{$this->mpesaURL}/oauth/v1/generate?grant_type=client_credentials");

        return $response->json()['access_token'];
        // });
    }


    public function stkPushRequest($phoneNumber, $amount, $accountReference)
    {
        $accessToken = $this->getAccessToken();

        $timestamp = now()->format('YmdHis');

        $password = base64_encode(
            $this->businessShortCode .
                $this->passkey .
                $timestamp
        );

        $url = "{$this->mpesaURL}/mpesa/stkpush/v1/processrequest";

        $transactionType = $this->transactionType === 'paybill'
            ? 'CustomerPayBillOnline'
            : 'CustomerBuyGoodsOnline';

        $payload = [
            'BusinessShortCode' => $this->businessShortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => $transactionType,
            'Amount'            => $amount,
            'PartyA'            => $phoneNumber,
            'PartyB'            => $this->businessShortCode,
            'PhoneNumber'       => $phoneNumber,
            'CallBackURL'       => $this->callBackURL,
            'AccountReference'  => $accountReference,
            'TransactionDesc'   => "#$accountReference"
        ];

        $response = Http::withToken($accessToken)->post($url, $payload);

        return $response->json();
    }

    public function queryStkPushStatus($checkoutRequestId)
    {
        $accessToken = $this->getAccessToken();

        $timestamp = now()->format('YmdHis');

        $shortcode = config('services.mpesa.shortcode');

        $passkey = config('services.mpesa.passkey');

        $password = base64_encode($shortcode . $passkey . $timestamp);

        $url = "{$this->mpesaURL}/mpesa/stkpushquery/v1/query";

        $response = Http::withToken($accessToken)->post($url, [
            'BusinessShortCode' => $shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ]);

        return $response->json();
    }
}
