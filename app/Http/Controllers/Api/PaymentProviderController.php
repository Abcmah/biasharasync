<?php

namespace App\Http\Controllers\Api;

use DB;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use App\Models\PaymentProvider;
use App\Http\Controllers\Controller;

class PaymentProviderController extends Controller
{
    public function index(Request $request)
    {

        $query = PaymentProvider::with('paymentMode');


        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Pagination parameters
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function ($provider) {
            return [
                'xid' => $provider->xid ?? (string) $provider->id,
                'name' => $provider->name,
                'code' => $provider->code,
                'env' => $provider->env,
                'is_active' => (bool) $provider->is_active,
                'priority' => $provider->priority ?? 1,
                'payment_mode_id' => $provider->paymentMode->xid,
                'payment_mode' => $provider->paymentMode ? [
                    'xid' => $provider->paymentMode->xid ?? (string) $provider->paymentMode->id,
                    'name' => $provider->paymentMode->name,
                    'mode_type' => $provider->paymentMode->mode_type,
                ] : null,
                'config' => $provider->config->config ?? [],
            ];
        })->values();


        $meta = [
            'paging' => [
                'total' => $paginator->total(),
                'links' => [],
            ],
            'time' => round(microtime(true) - LARAVEL_START, 3),
            'queries' => count(DB::getQueryLog()),
            'queries_list' => \DB::getQueryLog(),
        ];

        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    public function show($xid)
    {
        $id = Hashids::decode($xid)[0] ?? null;

        $provider = PaymentProvider::with('paymentMode')->findOrFail($id);

        return response()->json([
            'data' => [
                'xid' => $provider->xid,
                'name' => $provider->name,
                'code' => $provider->code,
                'payment_mode_id' => $provider->paymentMode
                    ? $provider->paymentMode->xid
                    : null,
                'env' => $provider->env ?? 'sandbox',
                'is_active' => (bool) $provider->is_active,
                'priority' => $provider->priority ?? 1,
            ]
        ]);
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required',
            'payment_mode_id' => 'required',
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'env' => 'required|in:sandbox,live',
            'priority' => 'nullable|integer',
            'is_active' => 'boolean',
            'config' => 'nullable|array',
        ]);

        // Decode the payment_mode_id safely
        $decodedId = Hashids::decode($data['payment_mode_id']);
        if (empty($decodedId)) {
            return response()->json(['message' => 'Invalid payment mode'], 422);
        }

        $paymentMode = PaymentMode::find($decodedId[0]);
        if (!$paymentMode) {
            return response()->json(['message' => 'Payment mode not found'], 404);
        }

        $company = company(); // your helper

        // Prepare data
        $payload = [
            'company_id' => $company->id,
            'payment_mode_id' => $paymentMode->id,
            'name' => $data['name'],
            'code' => $data['code'],
            'display_name' => $data['display_name'] ?? $data['name'],
            'env' => $data['env'],
            'priority' => $data['priority'] ?? 1,
            'is_active' => $data['is_active'] ?? true,
        ];

        DB::beginTransaction();

        try {

            $provider = PaymentProvider::create($payload);

            if (!empty($data['config'])) {
                $provider->config()->create([
                    'company_id' => $company->id,
                    'config' => $data['config'],
                ]);
            }

            DB::commit();
            $provider->load('config');

            return response()->json([
            'data' => $provider->fresh(['paymentMode', 'config'])
        ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create provider',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

public function update(Request $request, $xid)
{
    $data = $request->validate([
        // 'payment_mode_id' => 'required',
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:100',
        'env' => 'required|in:sandbox,live',
        'priority' => 'nullable|integer',
        'is_active' => 'boolean',
        'config' => 'nullable|array',
    ]);

    // Decode provider ID
    $decodedProviderId = Hashids::decode($xid);
    abort_if(empty($decodedProviderId), 404, 'Provider not found');

    $provider = PaymentProvider::with('config')->findOrFail($decodedProviderId[0]);

    // Decode payment mode
    // $decodedModeId = Hashids::decode($data['payment_mode_id']);
    // abort_if(empty($decodedModeId), 422, 'Invalid payment mode');

    // $paymentMode = PaymentMode::findOrFail($decodedModeId[0]);

    DB::beginTransaction();

    try {
        // Update provider fields
        $provider->update([
            // 'payment_mode_id' => $paymentMode->id,
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'env' => $data['env'],
            'priority' => $data['priority'] ?? 1,
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Update or create config (optional)
        if (array_key_exists('config', $data)) {
            if ($provider->config) {
                $provider->config->update([
                    'config' => $data['config'] ?? [],
                ]);
            } else {
                $provider->config()->create([
                    'config' => $data['config'] ?? [],
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'data' => $provider->fresh(['paymentMode', 'config'])
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to update payment provider',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function destroy($xid)
    {
        $decodedProviderId = Hashids::decode($xid);
        abort_if(empty($decodedProviderId), 404, 'Provider not found');

        $provider = PaymentProvider::with('config')->findOrFail($decodedProviderId[0]);
        $provider->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
