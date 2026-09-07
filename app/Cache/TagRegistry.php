<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * A DB-backed stand-in for Cache::tags(), which the database/file stores
 * don't support. Maps tag -> cache key so a tag flush can explicitly
 * forget every key registered under it.
 *
 * Flushing is done inside a transaction and deletes only the exact rows it
 * read, so a key registered by a concurrent attach() during the flush keeps
 * its registry row (and stays flushable next time) instead of being
 * orphaned in the cache with no way to reach it.
 */
class TagRegistry
{
    /**
     * Register a cache key under one or more tags. Idempotent — the
     * unique(tag, cache_key) index absorbs repeat calls.
     */
    public static function attach(string $cacheKey, array $tags): void
    {
        if (empty($tags)) {
            return;
        }

        $rows = array_map(fn ($tag) => [
            'tag'        => $tag,
            'cache_key'  => $cacheKey,
            'created_at' => now(),
        ], $tags);

        DB::table('cache_tag_registry')->insertOrIgnore($rows);
    }

    /**
     * Forget every cache key registered under a single tag.
     */
    public static function flush(string $tag): void
    {
        self::flushMany([$tag]);
    }

    /**
     * Forget every cache key registered under any of the given tags.
     */
    public static function flushMany(array $tags): void
    {
        $tags = array_values(array_filter(array_unique($tags)));

        if (empty($tags)) {
            return;
        }

        DB::transaction(function () use ($tags) {
            $rows = DB::table('cache_tag_registry')
                ->whereIn('tag', $tags)
                ->lockForUpdate()
                ->get(['id', 'cache_key']);

            if ($rows->isEmpty()) {
                return;
            }

            foreach ($rows->pluck('cache_key')->unique() as $key) {
                Cache::forget($key);
            }

            // Delete ONLY the rows we just processed. Rows a concurrent
            // attach() inserted after the SELECT are left in place; their
            // (freshly written) cache entries stay reachable for the next flush.
            DB::table('cache_tag_registry')
                ->whereIn('id', $rows->pluck('id'))
                ->delete();
        });
    }

    /**
     * Reaper for entries whose tags never get flushed. Rows carry the time
     * of their last (re)computation in created_at; anything older than
     * $olderThanSeconds has its cache entry forgotten and its row removed,
     * so a forever-stored entry can't be stranded — the entry and its
     * bookkeeping always go together.
     *
     * @return int rows deleted
     */
    public static function prune(int $olderThanSeconds): int
    {
        $rows = DB::table('cache_tag_registry')
            ->where('created_at', '<', now()->subSeconds($olderThanSeconds))
            ->get(['id', 'cache_key']);

        if ($rows->isEmpty()) {
            return 0;
        }

        foreach ($rows->pluck('cache_key')->unique() as $key) {
            Cache::forget($key);
        }

        return DB::table('cache_tag_registry')
            ->whereIn('id', $rows->pluck('id'))
            ->delete();
    }
}
