<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Article;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Article::query()
        ->where('status', 'brouillon')
        ->where('created_at', '<', now()->subDays(30))
        ->delete();
})->daily()->name('cleanup-old-drafts');
