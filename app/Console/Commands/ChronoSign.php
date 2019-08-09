<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

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
        $client = new Client([
            'timeout' => 10.0
        ]);

        $url = 'https://api.chrono.gg/quest/spin';

        try {
            $response = $client->request(
                'GET',
                $url,
                [
                    'headers' => [
                        'Authorization' => 'JWT eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImdhYXJhMTc1OUBnbWFpbC5jb20iLCJpZCI6ImRvbmdkb25nIiwidWlkIjoiNWQxOTdmOWQzZmMxYzkwMDEyMTc5MWMxIiwiaWF0IjoxNTYxOTUyMTg1LCJleHAiOjE1NjcxMzYxODUsImF1ZCI6Imh0dHBzOi8vd3d3LmNocm9uby5nZyIsImlzcyI6Imh0dHBzOi8vYXBpLmNocm9uby5nZyIsImp0aSI6IjUyOTA1NDE5MjNkYjQzNWViNzY1ZWRkZWNlZDc0NTAxIn0.jGDuLiUy_qoEbtHQmpBZ82_-yUTeTO6aMYzLzo_ZmEQ',
                        'Accept'        => 'application/json',
                        'User-Agent'    => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100',
                    ],
                    'allow_redirects' => true,
                ]
            );

            app('sentry')->captureMessage('fetch %s done', ['chrono sign'], [
                'level' => 'info',
                'extra' => [
                    'status_code' => $response->getStatusCode(),
                    'header'      => $response->getHeaders(),
                    'body'        => $response->getBody(),
                ]
            ]);
        } catch (Exception $e) {
            if (!in_array($e->response->statusCode, [200, 420]) && env('APP_ENV') == 'production' && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        }
    }
}
