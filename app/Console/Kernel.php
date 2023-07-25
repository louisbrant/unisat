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
        'App\Console\Commands\ClearUserUsages',
        'App\Console\Commands\ClearUnverifiedUsersCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:clear-user-usages')->monthly();
        $schedule->command('cron:clear-unverified-users')->dailyAt('03:00');
        $schedule->command('cache:clear')->weeklyOn(0, '4:00');
        $schedule->command('view:clear')->weeklyOn(0, '5:00');
        $schedule->command('auth:clear-resets')->weeklyOn(0, '6:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
