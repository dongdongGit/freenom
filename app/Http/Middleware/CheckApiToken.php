<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ProvidesConvenienceMethods;
use Illuminate\Support\Facades\Log;

class CheckApiToken
{
    use ProvidesConvenienceMethods;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header_signature = $request->header('X-Hub-Signature');

        if (empty($header_signature)) {
            return $this->error(40101);
        } else {
            $signature = 'sha1=' . hash_hmac('sha1', $request->getContent(), config('hook.secret'), false);

            if (!hash_equals($signature, $header_signature)) {
                Log::info('[error]验签失败');
                return $this->error(40102);
            }
        }

        return $next($request);
    }
}
