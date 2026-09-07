<?php

namespace App\Http\Resources\v1\Backend\Gallery;

use App\Http\Resources\v1\Backend\CreatedByResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryTypeResource extends JsonResource
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
            'name' => $this->when($this->name, $this->name),
            'sort_order' => $this->when(isset($this->sort_order), $this->sort_order),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'is_deletable' => $this->when(isset($this->is_deletable), $this->is_deletable),
            'created_at' => $this->when($this->created_at, fn() => $this->created_at->format('Y-m-d H:i:s')),
            'createdBy' => $this->whenLoaded('createdBy', fn() => new CreatedByResource($this->createdBy)),
            'updatedBy' => $this->whenLoaded('updatedBy', fn() => new CreatedByResource($this->updatedBy)),
        ];
    }
}
