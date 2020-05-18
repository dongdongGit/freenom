<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Sign;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Sentry\Severity;

class ChronoSign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sign:chrono';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动签到领金币';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = 'sign:chrono_coins';

        if (app('cache')->has($key)) {
            return;
        }

        $base_url = 'https://api.chrono.gg/';
        $admin = User::oldest('id')->firstOrFail();
        $data = [];

        try {
            $header = [
                'Authorization' => 'JWT ' . env('CHRONO_TOKEN'),
                'Accept'        => 'application/json',
                'User-Agent'    => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100',
            ];

            $data['sign_result'] = $result = fetch($base_url . 'quest/spin', [], $header);

            app('sentry')->configureScope(function ($scope) use ($result) {
                $scope->setLevel(new Severity())
                    ->setTag('sign', 'chrono')
                    ->setExtra('result', $result);
            });

            app('sentry')->captureMessage(
                'chrono sign done',
                new Severity('info')
            );

            $coins_result = fetch($base_url . 'account/coins', [], $header);
            $data['balance'] = Arr::get($coins_result, 'balance', 0);

            $data = [
                'success' => true,
                'message' => 'success',
                'type'    => 'chrono',
                'data'    => $data
            ];
            // TODO: result 为null 鉴权失效 为420 已经签过
        } catch (Exception $e) {
            if (env('APP_ENV') == 'production' && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }

            $data = [
                'success' => false,
                'message' => 'chrono 签到失败',
                'type'    => 'chrono',
                'data'    => []
            ];
        }

        $admin->notify(new Sign($data));
        app('cache')->put($key, 1, now()->endOfDay()->timestamp - now()->timestamp);
    }
}
