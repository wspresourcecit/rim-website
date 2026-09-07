<?php

namespace App\Services\v1\Backend;

use App\Models\PageHeader;

class PageHeaderService
{
    /**
     * Read the page header for the given page key, creating a default one if missing.
     */
    public function getPageHeader(array $data): PageHeader
    {
        $pageKey = $data['page_key'] ?? null;

        return PageHeader::cacheGet(
            $pageKey ?? 'default',
            fn () => $this->firstOrCreatePageHeader($pageKey),
        );
    }

    public function updatePageHeader(array $data): PageHeader
    {
        $pageKey = $data['page_key'];
        $pageHeader = $this->firstOrCreatePageHeader($pageKey);

        unset($data['page_key']);

        if (! empty($data['meta_og_image'])) {
            $data['meta_og_image'] = uploadBase64Image($data['meta_og_image'], 'images/cms/page-header', 'page-header');
            deleteImage($pageHeader->meta_og_image);
        } else {
            $data['meta_og_image'] = $pageHeader->meta_og_image;
        }

        $pageHeader->update($data);
        $pageHeader->refresh();

        PageHeader::cachePut($pageKey ?? 'default', $pageHeader);

        return $pageHeader;
    }

    private function firstOrCreatePageHeader(?string $pageKey): PageHeader
    {
        return PageHeader::firstOrCreate(
            ['page_key' => $pageKey],
            $this->defaultPageHeaderData($pageKey),
        );
    }

    private function defaultPageHeaderData(?string $pageKey): array
    {
        $label = ucfirst(str_replace('-', ' ', $pageKey ?? 'page'));

        return [
            'heading' => $label,
            'sub_heading' => $label,
        ];
    }
}
