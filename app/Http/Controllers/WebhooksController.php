<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class WebhooksController extends Controller
{
    public function webhooks(Request $request)
    {
        $signature = 'sha1=' . hash_hmac('sha1', $request->getContent(), env('SECRET_WEBHOOKS'), false);

        if (!hash_equals($signature, $request->header('X-Hub-Signature'))) {
            Log::info('[error]验签失败');
            return $this->error('验签失败');
        }

        Log::info(base_path());
        $process = new Process('cd ' . base_path() . '; ./deploy.sh');
        $process->run(function ($type, $buffer) {
            Log::info($buffer);
        });
    }
}
