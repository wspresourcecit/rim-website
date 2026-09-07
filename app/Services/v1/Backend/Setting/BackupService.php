<?php
namespace App\Services\v1\Backend\Setting;

use App\Models\Setting\WebsiteAsset;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Storage;

class BackupService
{
   const ERROR_MESSAGE = "Something was wrong!";

   public function getBackups()
   {
      try {
            $disk = Storage::disk('backups');
            $files = $disk->allFiles();

            $backups = [];

            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (!in_array($extension, ['sql', 'zip'])) {
                    continue;
                }

                $lastModified = $disk->lastModified($file);

                $backups[] = [
                    'file_name'   => basename($file),
                    'file_path'   => $file,
                    'file_type'   => strtoupper($extension),
                    'file_size'   => round($disk->size($file) / 1024 / 1024, 2) . ' MB',
                    'created_at'  => Carbon::createFromTimestamp($lastModified)->format('Y-m-d H:i:s'),
                    'timestamp'   => $lastModified,
                ];
            }

            usort($backups, function ($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });
            
            foreach ($backups as &$backup) {
                unset($backup['timestamp']);
            }

            $websiteAsset = WebsiteAsset::where('is_active', true)->select('is_auto_backup', 'backup_frequency')->first()
                ?? new WebsiteAsset();
            $websiteAsset->backups = $backups;
            
            return $websiteAsset;
      } catch (Exception $e) {
        throw new Exception(self::ERROR_MESSAGE, 500);
      }
   }

   public function createLocalBackup()
   {
      try {
            $filename = "backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
            $storagePath = Storage::disk('backups')->path('');

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.database')),
                escapeshellarg($storagePath . '/' . $filename)
            );

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new Exception('Failed to generate database dump.', 500);
            }

            return $filename;

        } catch (Exception $e) {
            throw new Exception(self::ERROR_MESSAGE, 500);
        }
   }

   public function downloadLocalFile($filename)
   {
      // The `backups` disk root is already storage/app/backups, so files
      // live at the disk root — do not prefix with 'backups/'.
      $filename = basename($filename);

      if (! Storage::disk('backups')->exists($filename)) {
         throw new Exception('Backup file not found.', 404);
      }

      return Storage::disk('backups')->download($filename);
   }

   public function deleteLocalFile($fileName)
   {
      try {
            $filePath = $fileName;
            if (Storage::disk('backups')->exists($filePath)) {
                Storage::disk('backups')->delete($filePath);
                return response()->json(['message' => 'Backup file deleted.']);
            }
      } catch (Exception $e) {
         throw new Exception(self::ERROR_MESSAGE, 500);
      }
   }

   public function createCloudBackup(array $data)
   {
        $WebsiteAsset = WebsiteAsset::query()->first();
        if($WebsiteAsset && $WebsiteAsset->is_auto_backup == $data['is_auto_backup'] && $WebsiteAsset->backup_frequency == $data['backup_frequency']){
             return true;
        }
        try {
                $backupConfig = WebsiteAsset::updateOrCreate(
                    ['is_active' => true],
                    [
                        'is_auto_backup' => $data['is_auto_backup'],
                        'backup_frequency' => $data['backup_frequency']
                    ]
                );

                if (!$backupConfig->is_auto_backup) {
                    return true;
                }

                Artisan::call('backup:db');

                return true;

            } catch (Exception $e) {
                throw new Exception(self::ERROR_MESSAGE, 500);
            }
    }
}