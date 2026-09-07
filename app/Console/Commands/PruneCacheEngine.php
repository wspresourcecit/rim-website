<?php

namespace App\Console\Commands;

use App\Cache\TagRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneCacheEngine extends Command
{
    protected $signature = 'cache:engine-prune
        {--registry-ttl=2592000 : Forget + drop tag-registry entries not recomputed in this many seconds}';

    protected $description = 'Reap stale tag-registry rows and expired entries in the active cache store (database or file).';

    public function handle(): int
    {
        $registryDeleted = TagRegistry::prune((int) $this->option('registry-ttl'));

        // Neither the database nor the file cache store garbage-collects
        // expired entries — a key that expires and is never read again just
        // sits there. Sweep them so the store stays bounded.
        $expired = match (config('cache.default')) {
            'database' => $this->pruneDatabaseStore(),
            'file' => $this->pruneFileStore(),
            default => 0,
        };

        $this->info("Pruned {$registryDeleted} tag-registry row(s) and {$expired} expired cache entr(y/ies).");

        return self::SUCCESS;
    }

    private function pruneDatabaseStore(): int
    {
        if (! DB::getSchemaBuilder()->hasTable('cache')) {
            return 0;
        }

        return DB::table('cache')->where('expiration', '<=', now()->getTimestamp())->delete();
    }

    private function pruneFileStore(): int
    {
        $dir = config('cache.stores.file.path', storage_path('framework/cache/data'));

        if (! is_dir($dir)) {
            return 0;
        }

        $now = now()->getTimestamp();
        $deleted = 0;

        // FileStore writes each entry as a 10-digit expiration timestamp
        // followed by the payload; '0000000000' means "forever".
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $handle = @fopen($file->getPathname(), 'r');
            if ($handle === false) {
                continue;
            }
            $expiration = (int) fread($handle, 10);
            fclose($handle);

            if ($expiration !== 0 && $expiration <= $now && @unlink($file->getPathname())) {
                $deleted++;
            }
        }

        return $deleted;
    }
}
