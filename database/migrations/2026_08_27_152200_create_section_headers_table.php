<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('section_headers', function (Blueprint $table) {
            $table->id();
           
            $table->enum('section_type', ['general', 'branch', 'online', 'offline'])->default('general'); // section_type : Global for all pages, branch, online, offline for specific pages
            $table->string('section_key'); // Will be store section name like : hero, why choose, course category
            $table->string('header_badge')->nullable();
            $table->string('header_title');
            $table->text('header_description')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Same section_key can repeat per branch (Home = branch_id null), but
            // only once within that branch context.
            $table->unique(['section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_headers');
    }
};
