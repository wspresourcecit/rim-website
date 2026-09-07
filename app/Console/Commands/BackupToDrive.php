<?php

namespace App\Console\Commands;

use App\Models\Setting\WebsiteAsset;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\v1\Backend\Setting\GoogleDriveService;

class BackupToDrive extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:db';

    /**
     * The console command description.
     */
    protected $description = 'Take database backup and upload it to Google Drive weekly if enabled.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $systemConfig = WebsiteAsset::first();
        if (!$systemConfig->is_auto_backup || $systemConfig->is_auto_backup === 'false' || $systemConfig->is_auto_backup === '0') {
            $this->info('Backup scheduler is disabled.');
            return 0;
        }

        $this->info('Starting Database Backup...');
        
        try {
                $filename = $systemConfig->backup_frequency."-backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
                $storagePath = storage_path("app/backups");

                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $localPath = $storagePath . '/' . $filename;

                // Database Dump
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s %s > %s',
                    escapeshellarg(config('database.connections.mysql.username')),
                    escapeshellarg(config('database.connections.mysql.password')),
                    escapeshellarg(config('database.connections.mysql.host')),
                    escapeshellarg(config('database.connections.mysql.database')),
                    escapeshellarg($localPath)
                );

                $output = [];
                $returnVar = null;
                exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    Log::error('Database Backup failed during mysqldump.');
                    return 1;
                }

                $driveService = new GoogleDriveService();
                $driveFileId = $driveService->uploadFile($localPath, $filename);

                if ($driveFileId) {
                    $this->info("Backup successfully uploaded to Google Drive. File ID: " . $driveFileId);
                    Log::info("Backup successfully uploaded to Google Drive. File ID: " . $driveFileId);
                    
                    if (file_exists($localPath)) {
                        unlink($localPath);
                    }
                } else {
                    $this->error("Failed to upload backup to Google Drive.");
                }

            } catch (\Exception $e) {
                Log::error('Backup Command Error: ' . $e->getMessage());
            }

        return 0;
    }
}
