<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use App\Models\PaymentIntent;
use App\Models\PaymentWebhook;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use function Psy\info;

class WebhookController extends Controller
{
    public function mpesaCallback(Request $request)
    {


        try {
            $callbackData = $request->all();

            if (isset($callbackData['Body']['stkCallback'])) {
                $stkCallback = $callbackData['Body']['stkCallback'];
                $checkoutRequestId = $stkCallback['CheckoutRequestID'];
                $resultCode = $stkCallback['ResultCode'];
                $resultDesc = $stkCallback['ResultDesc'];


                $intent = PaymentIntent::where('reference', $checkoutRequestId)->firstOrFail();


                PaymentWebhook::create([
                    'company_id' => $intent->company_id,
                    'payment_provider_id' => $intent->payment_provider_id,
                    'provider_reference' => $checkoutRequestId,
                    'provider_event' => 'stk_callback',
                    'payload' => $callbackData,
                    'status' => 'received',
                ]);

                if ($resultCode == 0) {
                    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

                    $mpesaReceiptNumber = $this->getCallbackValue($callbackMetadata, 'MpesaReceiptNumber');
                    $transactionDate = $this->getCallbackValue($callbackMetadata, 'TransactionDate');
                    $phoneNumber = $this->getCallbackValue($callbackMetadata, 'PhoneNumber');
                    $amount = $this->getCallbackValue($callbackMetadata, 'Amount');
                    $intent->update(['status' => 'completed']);
                } else {
                    $intent->update(['status' => 'failed']);

                    Log::warning(" Transaction FAILED: {$resultDesc}");
                }

                return response()->json([
                    'ResultCode' => 0,
                    'ResultDesc' => 'Accepted'
                ]);
            } else {
                Log::warning('Unknown callback format received:', $callbackData);
                return response()->json([
                    'ResultCode' => 0,
                    'ResultDesc' => 'Unknown format but accepted'
                ]);
            }
        } catch (\Exception $e) {
            Log::error(' Callback processing error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'ResultCode' => 0, // Still return success to M-Pesa to avoid retries
                'ResultDesc' => 'Error but accepted'
            ]);
        }
    }
    private function getCallbackValue($metadata, $key)
    {
        if (!is_array($metadata)) {
            return null;
        }

        foreach ($metadata as $item) {
            if (isset($item['Name']) && $item['Name'] === $key) {
                return $item['Value'] ?? null;
            }
        }
        return null;
    }
    public function stripeWebhook(Request $request)
    {
        $event = \Stripe\Webhook::constructEvent(
            $request->getContent(),
            $request->header('Stripe-Signature'),
            config('services.stripe.webhook_secret')
        );

        $session = $event->data->object;
        $reference = $session->metadata->payment_intent_ref ?? null;

        $intent = PaymentIntent::where('reference', $reference)->firstOrFail();

        PaymentWebhook::create([
            'company_id' => $intent->company_id,
            'payment_provider_id' => $intent->payment_provider_id,
            'provider_event' => $event->type,
            'provider_reference' => $session->id,
            'payload' => $request->all(),
            'status' => 'received',
        ]);

        if ($event->type === 'checkout.session.completed') {
            $intent->update(['status' => 'paid']);
            // order
        }

        return response()->json(['received' => true]);
    }
}
