<?php

namespace App\Services\v1\Backend\Gallery;

use App\Exceptions\ApiException;
use App\Models\Gallery\GalleryType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GalleryTypeService
{
    public function getActiveTypes(): Collection
    {
        return GalleryType::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getTypes(array $data): LengthAwarePaginator
    {
        return GalleryType::with(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->DataFilter($data)
            ->select('id', 'name', 'sort_order', 'is_active', 'is_deletable', 'created_at', 'updated_by', 'created_by')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeType(array $data): GalleryType
    {
        $type = GalleryType::create($data);
        return $this->loadData($type);
    }

    public function getType(GalleryType $galleryType): GalleryType
    {
        return $this->loadData($galleryType);
    }

    public function updateType(array $data, GalleryType $galleryType): GalleryType
    {
        $galleryType->update($data);
        return $this->loadData($galleryType);
    }

    public function deleteType(GalleryType $galleryType): bool
    {
        if (!$galleryType->is_deletable) {
            throw new ApiException("Could not delete!", 403);
        }

        return $galleryType->delete();
    }

    private function loadData(GalleryType $galleryType): GalleryType
    {
        return $galleryType->load(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }
}
