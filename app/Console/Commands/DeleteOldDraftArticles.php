<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Carbon\Carbon;

class DeleteOldDraftArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:delete-old-drafts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete draft articles that are older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $deletedCount = Article::where('status', 'draft')
            ->where('created_at', '<', $thirtyDaysAgo)
            ->delete();

        $this->info("Deleted {$deletedCount} old draft articles.");
    }
}
