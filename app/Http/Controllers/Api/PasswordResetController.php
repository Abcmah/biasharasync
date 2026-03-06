<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Examyou\RestAPI\ApiResponse;

class PasswordResetController extends Controller
{
    public function __construct(
        protected PasswordResetService $passwordResetService
    ) {}

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $result = $this->passwordResetService->sendResetLink($request->email);

        $statusCode = $result['status_code'] ?? 200;

        return ApiResponse::make($result['message'], [
            'success' => $result['success'],
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = $this->passwordResetService->resetPassword(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        if (!$result['success']) {
            $statusCode = $result['status_code'] ?? 422;

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $statusCode);
        }

        return ApiResponse::make($result['message'], [
            'success' => true,
        ]);
    }
}
