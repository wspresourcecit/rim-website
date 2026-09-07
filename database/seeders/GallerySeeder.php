<?php

namespace Database\Seeders;

use App\Models\Gallery\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'id' => 1,
                'created_by' => 1,
                'gallery_type_id' => 1,
                'title' => 'ট্রেনিং ল্যাব',
                'image' => 'images/cms/gallery/placeholder-1.jpg',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'created_by' => 1,
                'gallery_type_id' => 1,
                'title' => 'রিসেপশন এরিয়া',
                'image' => 'images/cms/gallery/placeholder-2.jpg',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'created_by' => 1,
                'gallery_type_id' => 1,
                'title' => 'ওয়ার্কস্টেশন',
                'image' => 'images/cms/gallery/placeholder-3.jpg',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'created_by' => 1,
                'gallery_type_id' => 2,
                'title' => 'ব্যাচ ২০২৪ বিদায় সংবর্ধনা',
                'image' => 'images/cms/gallery/placeholder-4.jpg',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'created_by' => 1,
                'gallery_type_id' => 2,
                'title' => 'সার্টিফিকেট বিতরণ',
                'image' => 'images/cms/gallery/placeholder-5.jpg',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 6,
                'created_by' => 1,
                'gallery_type_id' => 3,
                'title' => 'টেক কার্নিভাল',
                'image' => 'images/cms/gallery/placeholder-6.jpg',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'created_by' => 1,
                'gallery_type_id' => 3,
                'title' => 'সেমিনার ডে',
                'image' => 'images/cms/gallery/placeholder-7.jpg',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($galleries as $gallery) {
            Gallery::updateOrCreate(
                ['id' => $gallery['id']],
                $gallery
            );
        }
    }
}
