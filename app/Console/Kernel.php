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
        Commands\Removed::class,
        Commands\Testing::class,
        // Commands\Inspire::class,
    ];

    protected $routeMiddleware = [
        'logic' => \App\Http\Middleware\BusinessLogic::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Removed')->dailyAt('17:00')->sendOutputTo('/home/cabin/log/remove.txt');
        //$schedule->command('Testing')->everyMinute()->sendOutputTo('/home/cabin/log/test.txt');
    }
}
