<?php

namespace App\Jobs;

use App\Services\FreenomService;

class FreenomRenew extends Job
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($domains)
    {
        $this->domains = $domains;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $freenomService = new FreenomService();
        $freenomService->renew($this->domains);
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
