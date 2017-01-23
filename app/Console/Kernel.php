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
          \App\Console\Commands\SendReminderEmail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
      // $schedule->call('App\Http\Controllers\FollowUpController@sendReminder')->everyFiveMinutes();
      // $schedule->exec('App\Http\Controllers\FollowUpController@sendReminder')->everyMinute();
        $schedule->command('send:reminder')->timezone('America/New_York')->everyFiveMinutes();
    }
}
