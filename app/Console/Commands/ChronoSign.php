<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Sign;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
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
        $url = 'https://api.chrono.gg/quest/spin';

        try {
            $result = fetch($url, [], [
                'Authorization' => 'JWT ' . env('CHRONO_TOKEN'),
                'Accept'        => 'application/json',
                'User-Agent'    => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100',
            ]);

            app('sentry')->configureScope(function ($scope) use ($result) {
                $scope->setLevel(new Severity())
                    ->setTag('sign', 'chrono')
                    ->setExtra('result', $result);
            });

            app('sentry')->captureMessage(
                'chrono sign done',
                new Severity('info')
            );

            $admin = User::oldest('id')->firstOrFail();
            if (is_array($result)) {
                $coins = Arr::get($result, 'quest.value', 0) + Arr::get($result, 'quest.bonus', 0);
                $content = "chrono 签到获得 {$coins} 金币";

                if (!empty($chest = Arr::get($result, 'chest'))) {
                    $additional_coins = Arr::get($chest, 'base', 0) + Arr::get($chest, 'bonus');
                    $kind = Arr::get($chest, 'kind');
                    $sum_coins = $coins + $additional_coins;
                    $content .= ", 连续 {$kind} 天签到额外获得 {$additional_coins} 金币, 总共获得 {$sum_coins} 金币";
                }
            } else {
                $content = 'chrono 已经签到';
            }
            Notification::send($admin, new Sign($content));
            // TODO: result 为null 鉴权失效 为420 已经签过
        } catch (Exception $e) {
            if (env('APP_ENV') == 'production' && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        }
    }
}
