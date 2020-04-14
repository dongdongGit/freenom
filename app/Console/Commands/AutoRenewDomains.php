<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Domain;
use App\Jobs\FreenomRenew;
use Illuminate\Console\Command;

class AutoRenewDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freenom:auto_renew_domain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动续期freenom域名';

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
        $domains = Domain::where('enabled_auto_renew', 1)->get();

        $filtered = $domains->filter(function ($domain) {
            if (Carbon::parse($domain->expires_date)->lt(now()->addDay(14))) {
                return $domain;
            }
        });

        if ($filtered->isNotEmpty()) {
            dispatch(new FreenomRenew($filtered));
        }
    }
}
