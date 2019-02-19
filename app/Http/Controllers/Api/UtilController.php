<?php

namespace App\Http\Controllers\Api;

use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;

class UtilController extends Controller
{
    public function webhooks(Request $request)
    {
        $signature = 'sha1=' . hash_hmac('sha1', $request->getContent(), env('SECRET_WEBHOOKS'), false);

        if (!hash_equals($signature, $request->header('X-Hub-Signature'))) {
            Log::info('[error]验签失败');
            return $this->error('验签失败');
        }

        $process = new Process('cd ' . base_path() . '; chmod +x deploy.sh && ./deploy.sh');
        $process->run(function ($type, $buffer) {
            Log::info($buffer);
        });

        $data = $request->all();
        $composer_update_flag = true;
        $npm_update_flag = true;

        if (ends_with($data['ref'], 'master')) {
            foreach ($data['commits'] as $commit) {
                $search_data = array_merge($commit['added'], $commit['modified']);

                if ($composer_update_flag && in_array('composer.json', $search_data)) {
                    $composer_update_flag = false;
                }

                if ($npm_update_flag && in_array('package.json', $search_data)) {
                    $npm_update_flag = false;
                }
            }

            if (!$composer_update_flag) {
                $process = new Process('cd' . base_path() . ';composer update --no-interaction --no-dev --prefer-dist');
                $process->run(function ($type, $buffer) {
                    Log::info($buffer);
                });
            }

            if (!$npm_update_flag) {
                $process = new Process('cd' . base_path() . ';npm i && npm run production');
                $process->run(function ($type, $buffer) {
                    Log::info($buffer);
                });
            }
        }

        return $this->success();
    }
}
