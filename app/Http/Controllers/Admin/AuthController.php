<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;

class AuthController extends Controller
{
    public function login()
    {
        $credentials = request()->validate([
            'email'    => 'required|email|exists:admins,email',
            'password' => 'required|string'
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->error(42201);
        }

        $admin = Admin::where('email', $credentials['email'])->firstOrFail();

        return $this->success([
            'token'      => $token,
            'expires_in' => config('jwt.ttl') * 60,
            'user'       => $admin
        ]);
    }

    public function logout()
    {
        return $this->success();
    }
}
