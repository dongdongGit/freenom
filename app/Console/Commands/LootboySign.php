<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\Sign;
use Exception;
use Illuminate\Console\Command;
use Sentry\Severity;

class LootboySign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sign:lootboy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动签到';

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
        $admin = User::oldest('id')->firstOrFail();
        $key = 'sign:lootboy_diamond';

        if (app('cache')->has($key)) {
            return;
        }

        try {
            $base_url = 'https://api.lootboy.de/v1/offers';
            $rand = mt_rand(3, 8);
            $header = [
                'Authorization' => 'Bearer ' . env('LOOTBOY_TOKEN'),
                'Accept'        => 'application/json',
                'User-Agent'    => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/7{$rand}.0.3770.100",
            ];
            $result = fetch($base_url . '?lang=en', [], $header);

            if (is_array($result)) {
                foreach ($result as $offer) {
                    $offer_result = fetch("{$base_url}/{$offer['id']}?lang=en", [], $header, 'PUT');

                    app('sentry')->configureScope(function ($scope) use ($offer_result) {
                        $scope->setLevel(new Severity())
                            ->setTag('sign', 'lootboy')
                            ->setExtra('result', $offer_result)
                            ->setLevel(Severity::info());
                    });

                    app('sentry')->captureMessage("lootboy sign offer: {$offer['id']}");

                    $admin->notify(new Sign([
                        'success' => true,
                        'message' => 'success',
                        'type'    => 'lootboy',
                        'data'    => $offer_result
                    ]));
                }
            }
        } catch (Exception $e) {
            if (env('APP_ENV') == 'production' && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }

            $data = [
                'success' => false,
                'message' => 'lootboy 签到失败',
                'type'    => 'lootboy',
                'data'    => []
            ];

            $admin->notify(new Sign($data));
        }

        app('cache')->put($key, 1, now()->endOfDay()->timestamp - now()->timestamp);
    }
}
