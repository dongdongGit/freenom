<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    public function webhooks(Request $request)
    {
        $signature = 'sha1=' . hash_hmac('sha1', $request->getContent(), env('SECRET_WEBHOOKS'));

        if ($signature !== $request->header('X-Hub-Signature')) {
            Log::info('[error]验签失败');
            return $this->error();
        }
    }
}
