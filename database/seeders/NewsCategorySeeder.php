<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Award & Recognition', 'slug' => 'award-recognition'],
            ['id' => 2, 'name' => 'Event', 'slug' => 'event'],
            ['id' => 3, 'name' => 'CSR', 'slug' => 'csr'],
            ['id' => 4, 'name' => 'Press Release', 'slug' => 'press-release'],
            ['id' => 5, 'name' => 'Scholarship', 'slug' => 'scholarship'],
            ['id' => 6, 'name' => 'Notice', 'slug' => 'notice'],
            ['id' => 7, 'name' => 'Alumni', 'slug' => 'alumni'],
        ];

        foreach ($categories as $category) {
            NewsCategory::updateOrCreate(
                ['id' => $category['id']],
                $category + ['created_by' => 1, 'is_active' => true],
            );
        }
    }
}
