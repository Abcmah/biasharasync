<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Onboarding\CreateCompanyRequest;
use App\Models\SubscriptionPlan;
use App\Services\OnboardingService;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

class OnboardingController extends ApiBaseController
{
    public function __construct(
        private OnboardingService $onboardingService,
    ) {}

    /**
     * List available subscription plans for onboarding.
     */
    public function plans()
    {
        $plans = SubscriptionPlan::where('active', 1)
            ->where(function ($q) {
                $q->where('is_private', 0)->orWhereNull('is_private');
            })
            ->orderBy('position', 'asc')
            ->get();

        return ApiResponse::make('Plans fetched successfully', [
            'plans' => $plans,
        ]);
    }

    /**
     * Create a new company for the authenticated user.
     */
    public function createCompany(CreateCompanyRequest $request)
    {
        $user = auth('api')->user();

        if ($user->company_id) {
            return ApiResponse::make('Company already exists', [
                'already_onboarded' => true,
            ]);
        }

        try {
            $result = $this->onboardingService->createCompanyForUser(
                $user,
                $request->validated()
            );

            return ApiResponse::make('Company created successfully', $result);
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Onboarding: Company creation failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new ApiException(
                'Failed to create company. Please try again.'
            );
        }
    }
}
