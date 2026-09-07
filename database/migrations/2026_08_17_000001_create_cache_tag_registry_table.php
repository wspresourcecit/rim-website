<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * cache_tag_registry — maps a cache tag to the concrete cache keys stamped
 * with it, so list/collection caches can be invalidated by tag without a
 * tag-aware cache store (see App\Cache\TagRegistry).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cache_tag_registry', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->index();
            $table->string('cache_key');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['tag', 'cache_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_tag_registry');
    }
};
