<?php

namespace App\Http\Resources\v1\Backend\Gallery;

use App\Http\Resources\v1\Backend\CreatedByResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gallery_type_id' => $this->when($this->gallery_type_id, $this->gallery_type_id),
            'title' => $this->when($this->title, $this->title),
            'image' => $this->when($this->image, fn() => asset($this->image)),
            'sort_order' => $this->when(isset($this->sort_order), $this->sort_order),
            'gallery_type' => $this->whenLoaded('galleryType', fn() => new GalleryTypeResource($this->galleryType)),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'created_at' => $this->when($this->created_at, fn() => $this->created_at->format('Y-m-d H:i:s')),
            'createdBy' => $this->whenLoaded('createdBy', fn() => new CreatedByResource($this->createdBy)),
            'updatedBy' => $this->whenLoaded('updatedBy', fn() => new CreatedByResource($this->updatedBy)),
        ];
    }
}
