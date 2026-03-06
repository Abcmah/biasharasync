<?php

namespace App\Services;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Send a password reset link to the given email.
     *
     * Always returns a success-like message to prevent user enumeration.
     */
    public function sendResetLink(string $email): array
    {
        $status = Password::broker()->sendResetLink(['email' => $email]);

        // Always return success to prevent user enumeration.
        // Even if the email doesn't exist, we respond identically.
        if ($status === Password::RESET_LINK_SENT) {
            return [
                'success' => true,
                'message' => 'If your email is registered, you will receive a password reset link shortly.',
            ];
        }

        if ($status === Password::RESET_THROTTLED) {
            return [
                'success' => false,
                'message' => 'Please wait before requesting another reset link.',
                'status_code' => 429,
            ];
        }

        // For INVALID_USER or any other status, still return the same
        // generic message to avoid leaking whether the email exists.
        return [
            'success' => true,
            'message' => 'If your email is registered, you will receive a password reset link shortly.',
        ];
    }

    /**
     * Reset the user's password using the provided token.
     */
    public function resetPassword(array $credentials): array
    {
        $status = Password::broker()->reset(
            $credentials,
            function ($user, string $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return [
                'success' => true,
                'message' => 'Your password has been reset successfully.',
            ];
        }

        $messages = [
            Password::INVALID_TOKEN => 'The reset token is invalid or has expired.',
            Password::INVALID_USER => 'We could not find a user with that email address.',
            Password::RESET_THROTTLED => 'Please wait before retrying.',
        ];

        return [
            'success' => false,
            'message' => $messages[$status] ?? 'Unable to reset password.',
            'status_code' => $status === Password::RESET_THROTTLED ? 429 : 422,
        ];
    }
}
