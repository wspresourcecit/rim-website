<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'id' => 1,
                'category_id' => 3,
                'title' => 'Creative IT Supports Flood Victims Through Relief Funds',
                'slug' => 'creative-it-supports-flood-victims-through-relief-funds',
                'excerpt' => 'The relief aid has been already deployed to the flood-hit areas and the emergency response teams are ready with humanitarian assistance to fight further challenges.',
            ],
            [
                'id' => 2,
                'category_id' => 4,
                'title' => 'CBG Annual Business Plan (2024-2025)',
                'slug' => 'cbg-annual-business-plan-2024-2025',
                'excerpt' => 'Creative Business Group has organized a divergent program apart from its other academic and business programs, announcing the fiscal year operational plan.',
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'title' => 'Creative IT Institute Organized Promotional Ceremony',
                'slug' => 'creative-it-institute-organized-promotional-ceremony',
                'excerpt' => 'Creative IT Institute organized a promotional ceremony on July 27, 2024, to acknowledge the work and effort of their dedicated employees.',
            ],
            [
                'id' => 4,
                'category_id' => 4,
                'title' => 'Creative IT Institute Opens New Branch at Mirpur',
                'slug' => 'creative-it-institute-opens-new-branch-at-mirpur',
                'excerpt' => 'Creative IT Institute celebrated its continuous journey towards learning with the opening of a new branch at Mirpur 10, Dhaka on July 1.',
            ],
            [
                'id' => 5,
                'category_id' => 2,
                'title' => 'CITians Success Meetup - Season 1 (2024)',
                'slug' => 'citians-success-meetup-season-1-2024',
                'excerpt' => 'Creative IT Institute organizes CITians Success Meetup - Season 1 (2024) to celebrate the achievements of students who have made a successful remark in the IT industry.',
            ],
            [
                'id' => 6,
                'category_id' => 1,
                'title' => 'CBG, The Best Skill Training Institution in South Asia - 2024',
                'slug' => 'cbg-the-best-skill-training-institution-in-south-asia-2024',
                'excerpt' => 'Creative Business Group has won the Startup Excellence Award as the best skill training institution in South Asia.',
            ],
        ];

        foreach ($posts as $i => $post) {
            News::updateOrCreate(
                ['id' => $post['id']],
                array_merge([
                    'created_by' => 1,
                    'image' => 'images/cms/news/placeholder-'.$post['id'].'.jpg',
                    'body' => '<p>'.$post['excerpt'].'</p><p>Creative IT Institute continues to invest in people, community, and industry-ready skill development across Bangladesh.</p>',
                    'is_active' => true,
                    'published_at' => Carbon::now()->subDays(($i + 1) * 5),
                    'meta_title' => $post['title'].' - Creative IT Institute',
                    'meta_description' => $post['excerpt'],
                ], $post),
            );
        }
    }
}
