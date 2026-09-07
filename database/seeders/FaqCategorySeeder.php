<?php

namespace Database\Seeders;

use App\Models\Faq\FaqCategory;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'created_by' => 1,
                'name' => 'সাধারণ জিজ্ঞাসা',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'created_by' => 1,
                'name' => 'ভর্তি ও কোর্স',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'created_by' => 1,
                'name' => 'পেমেন্ট ও ফি',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            FaqCategory::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }
    }
}
