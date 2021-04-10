<?php


namespace App\Http\Controllers\API;



use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'device_name' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(['status:' => 'ok']);
    }

    public function verify(User $user, Request $request)
    {
        if ($request->get('token') != $user->verification_token){
            throw new Exception('Credentials are incorrect');
        }

        $user->email_verified_at = now();
        $user->save();

        return response(['status:' => 'ok']);
    }
}
