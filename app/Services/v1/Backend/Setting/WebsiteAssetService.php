<?php

namespace App\Services\v1\Backend\Setting;

use App\Models\Setting\WebsiteAsset;
use Exception;

class WebsiteAssetService
{
    const ERROR_MESSAGE = 'Something went wrong';

    /**
     * The single site-settings row, shared with every page via View::share.
     * Cached; refreshed in place by updateWebsiteAsset(). The `media` relation
     * is intentionally not loaded here — no consumer of this method uses it.
     */
    public function getWebsiteAsset(): ?WebsiteAsset
    {
        try {
            return WebsiteAsset::cacheGet(
                'default',
                fn() => WebsiteAsset::firstOrCreate(['is_active' => true], $this->prepareDefaultData()),
            );
        } catch (Exception $e) {
            throw new Exception(self::ERROR_MESSAGE, 500);
        }
    }

    public function updateWebsiteAsset(array $data): void
    {
        try {
            $websiteAsset = WebsiteAsset::firstOrCreate(['is_active' => true], $this->prepareDefaultData());
            if (isset($data['social_handles'])) {
                $data['social_handles'] = json_encode($data['social_handles']);
            }
            if (isset($data['favicon'])) {
                if ($websiteAsset->favicon) {
                    deleteImage($websiteAsset->favicon);
                }
                $data['favicon'] = $this->uploadImage($data['favicon'], 32, 32);
            } else {
                $data['favicon'] = $websiteAsset->favicon;
            }
            if (isset($data['main_logo'])) {
                if ($websiteAsset->main_logo) {
                    deleteImage($websiteAsset->main_logo);
                }
                $data['main_logo'] = $this->uploadImage($data['main_logo'], 240, 40);
            } else {
                $data['main_logo'] = $websiteAsset->main_logo;
            }
            if (isset($data['dark_logo'])) {
                if ($websiteAsset->dark_logo) {
                    deleteImage($websiteAsset->dark_logo);
                }
                $data['dark_logo'] = $this->uploadImage($data['dark_logo'], 240, 40);
            } else {
                $data['dark_logo'] = $websiteAsset->dark_logo;
            }
            $websiteAsset->update($data);
            $websiteAsset->refresh();

            WebsiteAsset::cachePut('default', $websiteAsset);
        } catch (Exception $e) {
            throw new Exception(self::ERROR_MESSAGE, 500);
        }
    }

    private function uploadImage($image, $width, $height): string
    {
        $slug = rand(000000, 999999);

        return uploadBase64Image(
            $image,
            'images/website',
            'image_' . $slug,
            $width,
            $height
        );
    }

    private function prepareDefaultData()
    {
        $socials = [
            [
                'platform_icon' => 'brand-facebook',
                'link' => 'https://www.facebook.com',
            ],
            [
                'platform_icon' => 'brand-instagram',
                'link' => 'https://www.instagram.com',
            ],
        ];

        return [

            'e_tin' => '570907703094',
            'trade_license' => 'TRAD/DSCC/228155/2019',
            'number' => '+8801830267638',
            'email' => 'info@domain.com',
            'address' => null,
            'map' => null,
            'map_title' => 'Cardiology Center',
            'copyright_text' => 'Creative Business Group. All rights reserved.',
            'timezone' => 'Asia/Dhaka',
            'maintenance_mode' => false,
            'maintenance_message' => 'Our website is currently undergoing scheduled maintenance to bring you an enhanced experience. We will be back online shortly.',
            'is_auto_backup' => false,
            'backup_frequency' => 'weekly',
            'social_handles' => json_encode($socials),
            'fb_page_follower' => '৭.১৫ লাখ ফলোয়ার',
            'youtube_subscriber' => '১ লাখ ৭৯ হাজার সাবস্ক্রাইবার',
            'fb_group_follower' => '৩ লাখ ৫০ হাজার মেম্বার',

        ];
    }

    public function getWebsiteAssets(): ?WebsiteAsset
    {
        try {
            $data = $this->prepareDefaultData();

            WebsiteAsset::firstOrCreate(
                ['is_active' => true],
                $data
            );

            return WebsiteAsset::where('is_active', true)
                ->first();
        } catch (Exception $e) {
            throw new Exception(self::ERROR_MESSAGE, 500);
        }
    }
}
