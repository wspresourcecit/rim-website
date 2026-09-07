<?php

namespace Database\Seeders;

use App\Models\Faq\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'id' => 1,
                'created_by' => 1,
                'category_id' => 2,
                'question' => 'আমি কীভাবে <span class="text-primary-50">Creative IT Institute</span>-এ ভর্তি হতে পারি?',
                'answer' => 'Creative IT Institute-এ ভর্তি হতে হলে, আপনার পছন্দের কোর্স নির্বাচন করে অনলাইনে বা নিকটস্থ ক্যাম্পাসে সরাসরি ভর্তি হতে পারবেন। প্রয়োজন হলে আমাদের কাউন্সেলরদের সহায়তা নিয়ে আপনার জন্য উপযুক্ত কোর্সটি নির্বাচন করতে পারবেন।',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'id' => 2,
                'created_by' => 1,
                'category_id' => 1,
                'question' => 'কোন কোর্সটি আমার জন্য সবচেয়ে উপযুক্ত হবে?',
                'answer' => 'আপনার আগ্রহ ও ক্যারিয়ার লক্ষ্য অনুযায়ী আমাদের কাউন্সেলররা আপনাকে সঠিক কোর্স নির্বাচনে সহায়তা করবেন।',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'id' => 3,
                'created_by' => 1,
                'category_id' => 1,
                'question' => 'ক্লাসগুলো কি অনলাইন, অফলাইন নাকি উভয় মাধ্যমেই হয়?',
                'answer' => 'আমাদের কোর্সসমূহ অনলাইন, অফলাইন এবং উভয় মাধ্যম মিলিয়ে হাইব্রিড পদ্ধতিতে পরিচালিত হয়, যাতে শিক্ষার্থীরা নিজের সুবিধামতো শিখতে পারেন।',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'id' => 4,
                'created_by' => 1,
                'category_id' => 1,
                'question' => 'ক্লাস মিস করলে কি পরে রেকর্ডেড ক্লাস দেখতে পারবো?',
                'answer' => 'হ্যাঁ, প্রতিটি ক্লাসের রেকর্ডিং পরবর্তীতে দেখার সুযোগ থাকে, যাতে কোনো শিক্ষার্থী পিছিয়ে না পড়ে।',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'id' => 5,
                'created_by' => 1,
                'category_id' => 1,
                'question' => 'কোর্স চলাকালীন কি মেন্টরদের কাছ থেকে সরাসরি সাপোর্ট পাওয়া যায়?',
                'answer' => 'হ্যাঁ, কোর্স চলাকালীন অভিজ্ঞ মেন্টরদের কাছ থেকে সরাসরি সাপোর্ট ও গাইডলাইন পাওয়া যায়।',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'id' => 6,
                'created_by' => 1,
                'category_id' => 1,
                'question' => 'কোর্স শেষ করার পর কি চাকরি বা ফ্রিল্যান্সিংয়ের জন্য ক্যারিয়ার সাপোর্ট পাওয়া যায়?',
                'answer' => 'হ্যাঁ, কোর্স শেষে ক্যারিয়ার প্লেসমেন্ট ও ফ্রিল্যান্সিং সহায়তা দিয়ে থাকে আমাদের ক্যারিয়ার সাপোর্ট টিম।',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'id' => 7,
                'created_by' => 1,
                'category_id' => 1,
                'question' => 'কোর্স শেষ করার পর কি Creative IT থেকে প্লেসমেন্টের সুযোগ রয়েছে?',
                'answer' => 'হ্যাঁ, কোর্স সম্পন্নকারী শিক্ষার্থীদের জন্য আমাদের পার্টনার প্রতিষ্ঠানসমূহে প্লেসমেন্টের সুযোগ রয়েছে।',
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['id' => $faq['id']],
                $faq
            );
        }
    }
}
