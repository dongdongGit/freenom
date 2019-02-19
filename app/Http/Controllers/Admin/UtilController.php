<?php

namespace App\Http\Controllers\Admin;

use Cache;
use App\Models\User;
use App\Http\Controllers\Controller;

class UtilController extends Controller
{
    public function index()
    {
        $user = $this->user();
        $cached_stats = Cache::remember('user_' . $this->user()->id, 15, function () use ($user) {
            $data = [
                'domain' => $user->domains()->count(),
                'user'   => User::count()
            ];

            return $data;
        });

        return $this->success($cached_stats);
    }
}
