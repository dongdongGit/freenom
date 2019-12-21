<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\AutoRenewDomains',
        'App\Console\Commands\CrawlPhoto',
        'App\Console\Commands\SaveImage',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('freenom:auto_renew_domain')->dailyAt('02:00');
        $schedule->command('sign:chrono')->dailyAt('08:' . mt_rand(min(max(now()->minute, 30), 45), 45));
        $schedule->command('sign:lootboy')->dailyAt('08:' . mt_rand(max(now()->minute, 46), 59));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
