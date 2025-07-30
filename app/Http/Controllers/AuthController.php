<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => ['required'],
                'password' => ['required'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function resetPassword(Request $request)
    {
        try {
            $userToReset = $request->validate([
                'username' => ['required', 'string', 'exists:users,username'],
            ]);
            $user = User::where('username', $userToReset['username'])->first();
            $newPassword = \Illuminate\Support\Str::random(8); // Generate a random password
            $user->password = bcrypt($newPassword);
            $user->save();
            Mail::to($user->email)->send(new PasswordResetEmail($user, $newPassword));

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
