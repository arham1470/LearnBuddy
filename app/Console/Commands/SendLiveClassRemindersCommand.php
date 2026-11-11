<?php

namespace App\Console\Commands;

use App\Jobs\SendLiveClassRemindersJob;
use Illuminate\Console\Command;

class SendLiveClassRemindersCommand extends Command
{
    protected $signature = 'reminders:live-classes {--window=60 : Reminder window in minutes}';

    protected $description = 'Scan upcoming live classes in Asia/Colombo timezone and dispatch reminders to enrolled students';

    public function handle(): int
    {
        $window = (int) $this->option('window');
        $this->info("Scanning upcoming live classes within {$window} minutes (Asia/Colombo)...");

        SendLiveClassRemindersJob::dispatchSync($window);

        $this->info('Live class reminder job completed.');

        return Command::SUCCESS;
    }
}
