<?php

namespace App\Cache;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Model caching in two flavours, one trait.
 *
 * 1. Singleton row (one config row, or one per branch / page context):
 *        $row = Model::cacheGet($context, fn () => …firstOrCreate…);
 *        Model::cachePut($context, $savedRow);   // after every write — warm write-through
 *        Model::cacheForget($context);
 *
 * 2. List / aggregate:
 *        $rows = Model::cacheRows($key, [Model::class.':list'], fn () => …->get());
 *        $val  = Model::cacheRememberWithTags($key, [Model::class.':list'], fn () => …);
 *
 * Any create / update / delete on the model forgets its list caches (via the
 * boot hooks below). When another model's cached list embeds THIS model's
 * data (a join / count / relation tree), declare it from THIS model's
 * booted() so a write here also flushes that list:
 *        static::invalidatesModelsBooted([Other::class]);
 * It is declared on the written model on purpose — a write always boots the
 * model it targets, so the listener is guaranteed to be registered (a
 * listener registered from the *other* side would be missed whenever that
 * side isn't loaded in the request, e.g. an admin API write).
 *
 * Values are stored as plain arrays (the app runs with
 * cache.serializable_classes = false), then rebuilt into models with
 * newFromBuilder() / hydrate().
 */
trait Cacheable
{
    /** Other models this one already flushes on write (per class, dedup guard). */
    protected static array $cacheDependenciesBooted = [];

    protected static function bootCacheable(): void
    {
        $flush = fn () => static::invalidateCache();

        static::created($flush);
        static::updated($flush);
        static::deleted($flush);

        if (method_exists(static::class, 'restored')) {
            static::restored($flush);
        }
    }

    /* --------------------------------------------------------------------
     |  Invalidation
     | ------------------------------------------------------------------ */

    /** Tags every list cache of this model is registered under. */
    protected static function cacheTags(): array
    {
        return [static::class, static::class.':list'];
    }

    /**
     * Forget every list cache for this model. Safe to call after a raw DB
     * write (pivot sync etc.) that fires no Eloquent event. A failure is
     * logged, never thrown — the entry then just expires by TTL.
     */
    public static function invalidateCache(): void
    {
        try {
            TagRegistry::flushMany(static::cacheTags());
        } catch (\Throwable $e) {
            Log::warning('Cache invalidation failed (entry will expire by TTL)', [
                'model' => static::class,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * When THIS model is written (created / updated / deleted), also forget
     * the given models' list caches — for their lists that join, count or
     * tree across this model. Call from booted().
     *
     * Reliable by construction: a write always boots the model it targets,
     * so booted() runs and this listener is registered every time.
     */
    public static function invalidatesModelsBooted(array $models = []): void
    {
        $new = array_values(array_diff($models, static::$cacheDependenciesBooted));

        if ($new === []) {
            return;
        }

        $flush = function () use ($new) {
            foreach ($new as $model) {
                $model::invalidateCache();
            }
        };

        static::saved($flush);
        static::deleted($flush);

        if (method_exists(static::class, 'restored')) {
            static::restored($flush);
        }

        static::$cacheDependenciesBooted = array_merge(static::$cacheDependenciesBooted, $new);
    }

    /* --------------------------------------------------------------------
     |  List / aggregate cache
     | ------------------------------------------------------------------ */

    /**
     * Cache a list query and return it as an Eloquent collection. Only raw
     * attributes are cached, then hydrate() rebuilds existing models.
     */
    public static function cacheRows(string $key, array $tags, Closure $callback, ?int $ttl = null): EloquentCollection
    {
        $rows = static::cacheRememberWithTags(
            $key,
            $tags,
            fn () => collect($callback())->map(fn ($model) => $model->getAttributes())->all(),
            $ttl,
        );

        return static::hydrate($rows);
    }

    /**
     * Like cacheRows(), but also caches and restores relations (has-many or
     * belongs-to, any depth via dot notation e.g. 'softwares.learningModes').
     * Raw attributes are stored for every model so JSON casts survive;
     * relations are put back with setRelation() so no lazy query fires.
     */
    public static function cacheTree(string $key, array $tags, array $relations, Closure $callback, ?int $ttl = null): EloquentCollection
    {
        $spec = static::relationSpec($relations);

        $rows = static::cacheRememberWithTags(
            $key,
            $tags,
            fn () => collect($callback())->map(fn ($model) => static::snapshotModel($model, $spec))->all(),
            $ttl,
        );

        return static::hydrate(array_map(fn ($row) => static::stripRelationKeys($row), $rows))
            ->each(fn ($model, $i) => static::restoreRelations($model, $rows[$i], $spec));
    }

    /**
     * Single-model variant of cacheTree() (for a route-bound detail record).
     */
    public static function cacheTreeOne(string $key, array $tags, array $relations, Closure $callback, ?int $ttl = null): ?static
    {
        $spec = static::relationSpec($relations);

        $row = static::cacheRememberWithTags(
            $key,
            $tags,
            fn () => ($m = $callback()) ? static::snapshotModel($m, $spec) : null,
            $ttl,
        );

        if ($row === null) {
            return null;
        }

        $model = (new static)->newFromBuilder(static::stripRelationKeys($row));
        static::restoreRelations($model, $row, $spec);

        return $model;
    }

    /** Turn ['a', 'b.c', 'b.d'] into ['a' => [], 'b' => ['c' => [], 'd' => []]]. */
    protected static function relationSpec(array $relations): array
    {
        $spec = [];
        foreach ($relations as $path) {
            $ref = &$spec;
            foreach (explode('.', $path) as $part) {
                $ref[$part] ??= [];
                $ref = &$ref[$part];
            }
            unset($ref);
        }

        return $spec;
    }

    protected static function snapshotModel(Model $model, array $spec): array
    {
        $data = $model->getAttributes();

        foreach ($spec as $relation => $children) {
            if (! $model->relationLoaded($relation)) {
                continue;
            }
            $value = $model->getRelation($relation);
            $data['__rel__'.$relation] = $value instanceof Collection
                ? ['many' => $value->map(fn ($m) => static::snapshotModel($m, $children))->all()]
                : ['one' => $value ? static::snapshotModel($value, $children) : null];
        }

        return $data;
    }

    protected static function stripRelationKeys(array $row): array
    {
        return array_filter($row, fn ($k) => ! str_starts_with($k, '__rel__'), ARRAY_FILTER_USE_KEY);
    }

    protected static function restoreRelations(Model $model, array $row, array $spec): void
    {
        foreach ($spec as $relation => $children) {
            $key = '__rel__'.$relation;
            if (! array_key_exists($key, $row)) {
                continue;
            }
            $snapshot = $row[$key];
            $related = $model->{$relation}()->getRelated();

            $rebuild = function (array $attrs) use ($related, $children) {
                $child = $related->newFromBuilder(static::stripRelationKeys($attrs));
                static::restoreRelations($child, $attrs, $children);

                return $child;
            };

            if (array_key_exists('many', $snapshot)) {
                $model->setRelation($relation, $related->newCollection(array_map($rebuild, $snapshot['many'])));
            } else {
                $model->setRelation($relation, $snapshot['one'] === null ? null : $rebuild($snapshot['one']));
            }
        }
    }

    /* --------------------------------------------------------------------
     |  Cached route-model binding (slug lookups only)
     | ------------------------------------------------------------------ */

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        // Only the frontend's {model:slug} routes are cached; admin binds by
        // id and must stay fresh (it's the base for writes).
        if ($field !== 'slug') {
            return parent::resolveRouteBinding($value, $field);
        }

        $attributes = static::cacheRememberWithTags(
            'route:slug:'.$value,
            [static::class.':list'],
            fn () => static::where($field, $value)->first()?->getAttributes(),
        );

        return $attributes === null ? null : (new static)->newFromBuilder($attributes);
    }

    /**
     * Cache any value under a tagged key. The key is registered with its tags
     * on a genuine miss only, so a hit costs one lookup.
     */
    public static function cacheRememberWithTags(string $key, array $tags, Closure $callback, ?int $ttl = null)
    {
        $fullKey = static::class.':'.$key;

        return Cache::remember($fullKey, static::cacheTtl($ttl), function () use ($fullKey, $tags, $callback) {
            $value = $callback();
            TagRegistry::attach($fullKey, $tags);

            return $value;
        });
    }

    /* --------------------------------------------------------------------
     |  Singleton-row cache (read + synchronous write-through)
     | ------------------------------------------------------------------ */

    /** Cached read. $seed builds the row on the first miss (may return null). */
    public static function cacheGet(string $context, Closure $seed): ?static
    {
        $attributes = Cache::remember(
            static::singletonCacheKey($context),
            static::cacheTtl(null),
            fn () => $seed()?->getAttributes(),
        );

        return $attributes === null ? null : (new static)->newFromBuilder($attributes);
    }

    /** Write-through: refresh the cached copy from a freshly saved row. */
    public static function cachePut(string $context, Model $row): void
    {
        Cache::put(static::singletonCacheKey($context), $row->getAttributes(), static::cacheTtl(null));
    }

    public static function cacheForget(string $context): void
    {
        Cache::forget(static::singletonCacheKey($context));
    }

    protected static function singletonCacheKey(string $context): string
    {
        return static::class.':singleton:'.$context;
    }

    /* --------------------------------------------------------------------
     |  Helpers
     | ------------------------------------------------------------------ */

    /**
     * TTL for a read that passes none: config('cache.engine.default_ttl').
     * Bounded (not "forever") so a stale entry self-heals within the window
     * if invalidation ever fails. Pass 0 for a genuine forever entry.
     */
    protected static function cacheTtl(?int $ttl): ?int
    {
        if ($ttl === 0) {
            return null;
        }

        return $ttl ?? ((int) config('cache.engine.default_ttl', 3600) ?: null);
    }
}
