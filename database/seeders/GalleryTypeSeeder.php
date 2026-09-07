<?php

namespace Database\Seeders;

use App\Models\Gallery\GalleryType;
use Illuminate\Database\Seeder;

class GalleryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'id' => 1,
                'created_by' => 1,
                'name' => 'আমাদের প্রাঙ্গণ',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'created_by' => 1,
                'name' => 'বিদায় সংবর্ধনা',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'created_by' => 1,
                'name' => 'ইভেন্টস',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'created_by' => 1,
                'name' => 'আমাদের অবকাঠামো',
                'sort_order' => 4,
                'is_active' => true,
                'is_deletable' => false,
            ],
        ];

        foreach ($types as $type) {
            GalleryType::updateOrCreate(
                ['id' => $type['id']],
                $type
            );
        }
    }
}
