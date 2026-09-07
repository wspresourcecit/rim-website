<?php

namespace App\Http\Resources\v1\Backend\News;

use App\Http\Resources\v1\Backend\CreatedByResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->when($this->slug, $this->slug),
            'excerpt' => $this->when($this->excerpt, $this->excerpt),
            'body' => $this->when($this->body, $this->body),
            'image' => $this->when($this->image, fn () => asset($this->image)),
            'published_at' => $this->when($this->published_at, fn () => $this->published_at->format('Y-m-d H:i:s')),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'meta_title' => $this->when($this->meta_title, $this->meta_title),
            'meta_description' => $this->when($this->meta_description, $this->meta_description),
            'meta_keywords' => $this->when($this->meta_keywords, $this->meta_keywords),
            'category' => $this->whenLoaded('category', fn () => new NewsCategoryResource($this->category)),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at->format('Y-m-d H:i:s')),
            'createdBy' => $this->whenLoaded('createdBy', fn () => new CreatedByResource($this->createdBy)),
            'updatedBy' => $this->whenLoaded('updatedBy', fn () => new CreatedByResource($this->updatedBy)),
        ];
    }
}
