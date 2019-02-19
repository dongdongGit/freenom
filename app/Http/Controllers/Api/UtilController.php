<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Jobs\Webhooks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UtilController extends Controller
{
    public function webhooks(Request $request)
    {
        $signature = 'sha1=' . hash_hmac('sha1', $request->getContent(), env('SECRET_WEBHOOKS'), false);

        if (!hash_equals($signature, $request->header('X-Hub-Signature'))) {
            Log::info('[error]验签失败');
            return $this->error('验签失败');
        }

        dispatch(new Webhooks($request->all()));

        return $this->success();
    }
}
