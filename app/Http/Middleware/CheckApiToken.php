<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $signature = 'sha1=' . hash_hmac('sha1', $request->getContent(), config('hook.secret'), false);

        if (!hash_equals($signature, $request->header('X-Hub-Signature'))) {
            Log::info('[error]验签失败');
            return $this->error('403', [], '签名错误');
        }

        return $next($request);
    }
}
