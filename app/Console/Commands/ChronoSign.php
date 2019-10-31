<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Sentry\Severity;

class ChronoSign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chrono:sign';

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
                    ->setTag('chrono', 'sign')
                    ->setExtra('result', $result);
            });

            app('sentry')->captureMessage(
                'chrono sign done',
                new Severity('info')
            );
        } catch (Exception $e) {
            if (env('APP_ENV') == 'production' && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        }
    }
}
