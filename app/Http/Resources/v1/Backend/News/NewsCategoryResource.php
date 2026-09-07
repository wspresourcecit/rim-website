<?php

namespace App\Http\Resources\v1\Backend\News;

use App\Http\Resources\v1\Backend\CreatedByResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsCategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->when($this->slug, $this->slug),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'news_count' => $this->when(isset($this->news_count), fn () => (int) $this->news_count),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at->format('Y-m-d H:i:s')),
            'createdBy' => $this->whenLoaded('createdBy', fn () => new CreatedByResource($this->createdBy)),
            'updatedBy' => $this->whenLoaded('updatedBy', fn () => new CreatedByResource($this->updatedBy)),
        ];
    }
}
