<?php

namespace Database\Seeders;

use App\Models\Setting\Extension;
use Illuminate\Database\Seeder;

class ExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $extensions = [
            [
                'id'           => 1,
                'name'         => 'reCAPTCHA',
                'short_code'   => 'recaptcha',
                'credentials'  => [
                    ['key' => 'site_key', 'value' => '6Le1pUgtAAAAAOxI2miAZFGgs-1W5V595sTbH0ho'],
                    ['key' => 'secret_key', 'value' => '6Le1pUgtAAAAANrw-lPrS3hWPGubAOhBFeU5TGh2'],
                ],
                'instructions' => '<p>Instruction</p>',
                'is_api'       => 1,
                'is_active'    => 1,
                'created_at'   => '2026-06-30 17:52:00',
                'updated_at'   => '2026-08-06 15:33:04',
            ],
            [
                'id'           => 2,
                'name'         => 'Google Analytics',
                'short_code'   => 'google_analytics',
                'credentials'  => [
                    ['key' => 'measurement_id', 'value' => 'G-FHDDG1W2EC'],
                ],
                'instructions' => '<p>google_analytics</p>',
                'is_api'       => 1,
                'is_active'    => 1,
                'created_at'   => '2026-06-30 21:12:20',
                'updated_at'   => '2026-06-30 21:12:20',
            ],
            [
                'id'           => 3,
                'name'         => 'Meta Pixel',
                'short_code'   => 'meta_pixel',
                'credentials'  => [
                    ['key' => 'pixel_id', 'value' => 'fdggdfgghhds'],
                ],
                'instructions' => null,
                'is_api'       => 1,
                'is_active'    => 1,
                'created_at'   => '2026-06-30 21:53:12',
                'updated_at'   => '2026-06-30 21:53:12',
            ],
            [
                'id'           => 4,
                'name'         => 'Microsoft Clarity',
                'short_code'   => 'clarity',
                'credentials'  => [
                    ['key' => 'project_id', 'value' => 'dfgdfgdfg'],
                ],
                'instructions' => null,
                'is_api'       => 1,
                'is_active'    => 1,
                'created_at'   => '2026-06-30 21:55:09',
                'updated_at'   => '2026-06-30 21:55:09',
            ],
            [
                'id'           => 5,
                'name'         => 'Google Drive',
                'short_code'   => 'google_drive',
                'credentials'  => [
                    ['key' => 'client_id', 'value' => '426893883367-ssbl2l0imflars5mjs3hbvigi2u0fl2r.apps.googleusercontent.com'],
                    ['key' => 'client_secret', 'value' => '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'],
                    ['key' => 'refresh_token', 'value' => '1//04zXxKsXhe_svCgYIARAAGAQSNwF-L9IruHtMdDqizJj1sgdn6eIqkY6newgt2MqL7bresOlL-rAlsFxcBdz0176Gs8OzP3YPDyM'],
                    ['key' => 'folder_id', 'value' => '1zxgVvRn-DsJiHZAYePqCSBn7aMoZ7WC1'],
                ],
                'instructions' => '<p>Instruction</p>',
                'is_api'       => 1,
                'is_active'    => 1,
                'created_at'   => '2026-07-12 15:19:06',
                'updated_at'   => '2026-07-14 23:51:03',
            ],
        ];

        foreach ($extensions as $extension) {
            Extension::updateOrCreate(
                ['id' => $extension['id']],
                $extension
            );
        }
    }
}