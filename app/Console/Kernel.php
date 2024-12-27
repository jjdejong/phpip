<?php

protected $commands = [
    \App\Console\Commands\RenewrSync::class,
    \App\Console\Commands\SendTasksDueEmail::class,
];
    
protected function schedule(Schedule $schedule)
{
    $schedule->command('tasks:send-due-email')->weeklyOn(1, '6:00');
    $schedule->command('tasks:renewr-sync --demo')->weeklyOn(1, '3:00');
}
// Dont forget to set a cron job to run the scheduler. Add the following line to your cron file:
// (For every 30 minutes)
// */30 * * * * /var/www/phpip/artisan schedule:run >> /dev/null 2>&1