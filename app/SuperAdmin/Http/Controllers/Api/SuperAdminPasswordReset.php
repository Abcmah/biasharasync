<?php

namespace App\SuperAdmin\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class SuperAdminPasswordReset extends Controller
{


    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );


        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['error' => ['message' => __($status)]], 400);
    }
    /**
     * Send a reset link to the given user.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => trans($status)])
            : response()->json(['success' => false, 'message' => trans($status)], 422);
    }
    // if ($status == Password::RESET_LINK_SENT) {
    //     return response()->json(['status'=> __($status)]);
    // }

    // return $status === Password::RESET_LINK_SENT? response()->json(['success' => true, 'message' => trans($status)]) : response()->json(['success' => false, 'message' => trans($status)], 422);
    public function resdet(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        // This Laravel helper handles token verification and user lookup
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {

                $user->forceFill([
                    'password' => $password
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        // Return response based on Laravel's internal status constants
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => trans($status)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => trans($status)
        ], 422);
    }
}
