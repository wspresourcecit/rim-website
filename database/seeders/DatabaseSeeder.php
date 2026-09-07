<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RPSeeder::class,
            UserSeeder::class,
            ExtensionSeeder::class,

            NewsCategorySeeder::class,
            NewsSeeder::class,

            FaqCategorySeeder::class,
            FaqSeeder::class,
            GalleryTypeSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
