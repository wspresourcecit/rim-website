<?php

namespace App\Services\v1\Backend\Gallery;

use App\Models\Gallery\Gallery;
use App\Models\Gallery\GalleryType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GalleryService
{
    private const UPLOAD_DIR = 'images/cms/gallery';

    public function getGalleries(array $data): LengthAwarePaginator
    {
        return Gallery::with(['galleryType:id,name', 'createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->DataFilter($data)
            ->select('id', 'gallery_type_id', 'title', 'image', 'sort_order', 'is_active', 'created_at', 'updated_by', 'created_by')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeGallery(array $data): Gallery
    {
        $data['image'] = uploadBase64Image($data['image'], self::UPLOAD_DIR, 'gallery');
        $gallery = Gallery::create($data);

        return $this->loadData($gallery);
    }

    public function getGallery(Gallery $gallery): Gallery
    {
        return $this->loadData($gallery);
    }

    public function updateGallery(array $data, Gallery $gallery): Gallery
    {
        if (! empty($data['image'])) {
            $data['image'] = uploadBase64Image($data['image'], self::UPLOAD_DIR, 'gallery');
            deleteImage($gallery->image);
        } else {
            unset($data['image']);
        }
        $gallery->update($data);

        return $this->loadData($gallery);
    }

    public function deleteGallery(Gallery $gallery): bool
    {
        deleteImage($gallery->image);

        return $gallery->delete();
    }

    private function loadData(Gallery $gallery): Gallery
    {
        return $gallery->load(['galleryType:id,name', 'createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }

    public function getTypeGallery(string $type)
    {
        return Gallery::cacheRows(
            'type:'.md5($type),
            [Gallery::class.':list', GalleryType::class.':list'],
            function () use ($type) {
                $galleryType = GalleryType::where('name', $type)->first();

                if (! $galleryType) {
                    return collect();
                }

                return Gallery::where('gallery_type_id', $galleryType->id)
                    ->select('id', 'title', 'image')
                    ->get();
            },
        );
    }
}
