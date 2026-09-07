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
        Schema::create('website_assets', function (Blueprint $table) {
            $table->id();
            $table->text('footer_text')->nullable();
            $table->string('e_tin')->nullable();
            $table->string('trade_license')->nullable();
            $table->string('number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('map_title')->nullable();
            $table->text('map')->nullable();
            $table->string('copyright_text')->nullable();
            $table->json('social_handles')->nullable();
            $table->string('fb_page_follower')->nullable();
            $table->string('youtube_subscriber')->nullable();
            $table->string('fb_group_follower')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('maintenance_mode')->default(false);
            $table->longText('maintenance_message')->nullable();
            $table->boolean('is_auto_backup')->default(false);
            $table->enum('backup_frequency', ['daily', 'weekly', 'monthly'])->default('weekly');

            $table->string('favicon')->nullable();
            $table->string('main_logo')->nullable();
            $table->string('dark_logo')->nullable();

            $table->string('floating_facebook_link')->nullable();
            $table->string('floating_instagram_link')->nullable();
            $table->string('floating_whatsapp_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_assets');
    }
};
