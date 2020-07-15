<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Webhooks extends Job
{
    protected $data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

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
        if (!Str::endsWith($this->data['ref'], 'master')) {
            return;
        }

        $process = Process::fromShellCommandline('chmod u+x deploy.sh && sh ./deploy.sh');
        $process->run(function ($type, $buffer) {
            info($buffer);
        });

        $composer_update_flag = true;
        $npm_update_flag = true;
        $npm_run_flag = true;

        foreach ($this->data['commits'] as $commit) {
            $search_data = array_merge($commit['added'], $commit['modified'], $commit['removed']);

            if ($composer_update_flag && in_array('composer.json', $search_data)) {
                $composer_update_flag = false;
            }

            if ($npm_update_flag && in_array('package.json', $search_data)) {
                $npm_update_flag = false;
            }

            if ($npm_run_flag && preg_grep('/^resources\/(js|sass)\/.+$/', $search_data)) {
                $npm_run_flag = false;
            }
        }

        if (!$composer_update_flag) {
            $process = Process::fromShellCommandline('composer update --no-interaction --no-dev --prefer-dist');
            $process->setTimeout(300);
            $process->run(function ($type, $buffer) {
                info($buffer);
            });
        }

        if (!$npm_update_flag) {
            $process = Process::fromShellCommandline('npm i && npm run production');
            $process->setTimeout(300);
            $process->run(function ($type, $buffer) {
                info($buffer);
            });
        } elseif (!$npm_run_flag) {
            $process = Process::fromShellCommandline('npm run production');
            $process->setTimeout(300);
            $process->run(function ($type, $buffer) {
                info($buffer);
            });
        }
    }
}
