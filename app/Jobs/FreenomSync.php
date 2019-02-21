<?php

namespace App\Jobs;

use App\Services\FreenomService;

class FreenomSync extends Job
{
    public $user;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $uesr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $freenomService = new FreenomService();
        $freenomService->sync($this->user);
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addSeconds(3);
    }
}
