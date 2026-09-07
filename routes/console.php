<?php

use App\Models\Setting\WebsiteAsset;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clear orphaned cache-engine bookkeeping rows (keys that expired by TTL
// instead of being flushed, and long-dead row-level version counters).
Schedule::command('cache:engine-prune')->daily();

Schedule::command('backup:db')
    ->daily()
    ->runInBackground()
    ->when(function () {
        $config = WebsiteAsset::first();

        if (!$config || !$config->is_auto_backup || $config->is_auto_backup === '0' || $config->is_auto_backup === 'false') {
            return false;
        }

        $frequency = $config->backup_frequency; // 'daily', 'weekly', 'monthly'

        if ($frequency === 'daily') {
            return true;
        }

        if ($frequency === 'weekly') {
            return now()->isFriday();
        }

        if ($frequency === 'monthly') {
            return now()->day === 1;
        }

        return false;
    });
