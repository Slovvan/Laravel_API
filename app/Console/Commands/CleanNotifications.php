<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old notifications older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Since notifications table doesn't exist, just log the execution
        $this->info('Notifications cleanup task executed (no notifications table present).');
    }
}
