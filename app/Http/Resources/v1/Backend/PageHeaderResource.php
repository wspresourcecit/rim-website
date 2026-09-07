<?php

namespace App\Http\Resources\v1\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageHeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page_key' => $this->page_key,
            'heading' => $this->heading,
            'sub_heading' => $this->sub_heading,
            'meta_title' => $this->when($this->meta_title, $this->meta_title),
            'meta_description' => $this->when($this->meta_description, $this->meta_description),
            'meta_keywords' => $this->when($this->meta_keywords, $this->meta_keywords),
   
            'meta_og_image' => $this->when($this->meta_og_image, fn() => asset($this->meta_og_image)),
            'created_at' => $this->when($this->created_at, fn() => $this->created_at?->format('Y-m-d H:i:s')),
        ];
    }
}
