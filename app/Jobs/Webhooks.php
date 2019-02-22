<?php

namespace App\Jobs;

use Log;
use Symfony\Component\Process\Process;

class Webhooks extends Job
{
    protected $data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;

        if (ends_with($data['ref'], 'master')) {
            $process = new Process('cd ' . base_path() . '; chmod +x deploy.sh && ./deploy.sh');
            $process->run(function ($type, $buffer) {
                Log::info($buffer);
            });

            $composer_update_flag = true;
            $npm_update_flag = true;
            $npm_run_flag = true;

            foreach ($data['commits'] as $commit) {
                $search_data = array_merge($commit['added'], $commit['modified']);

                if ($composer_update_flag && in_array('composer.json', $search_data)) {
                    $composer_update_flag = false;
                }

                if ($npm_update_flag && in_array('package.json', $search_data)) {
                    $npm_update_flag = false;
                }

                if ($npm_run_flag && preg_grep('/^resources\/.+$/', $search_data)) {
                    $npm_run_flag = false;
                }
            }

            if (!$composer_update_flag) {
                $process = new Process('cd ' . base_path() . ';composer update --no-interaction --no-dev --prefer-dist');
                $process->setTimeout(300);
                $process->run(function ($type, $buffer) {
                    Log::info($buffer);
                });
            }

            if (!$npm_update_flag) {
                $process = new Process('cd ' . base_path() . ';npm i && npm run prod');
                $process->setTimeout(300);
                $process->run(function ($type, $buffer) {
                    Log::info($buffer);
                });
            } elseif (!$npm_run_flag) {
                $process = new Process('cd ' . base_path() . ';npm run prod');
                $process->setTimeout(90);
                $process->run(function ($type, $buffer) {
                    Log::info($buffer);
                });
            }
        }
    }
}
