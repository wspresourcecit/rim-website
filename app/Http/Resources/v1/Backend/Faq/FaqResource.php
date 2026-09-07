<?php

namespace App\Http\Resources\v1\Backend\Faq;

use App\Http\Resources\v1\Backend\CreatedByResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'category_id' => $this->when($this->category_id, $this->category_id),
            'question' => $this->when($this->question, $this->question),
            'answer' => $this->when($this->answer, $this->answer),
            'category' => $this->whenLoaded('category', fn() => new FaqCategoryResource($this->category)),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'is_featured' => $this->when(isset($this->is_featured), $this->is_featured),
            'created_at' => $this->when($this->created_at, fn() => $this->created_at->format('Y-m-d H:i:s')),
            'createdBy' => $this->whenLoaded('createdBy', fn() => new CreatedByResource($this->createdBy)),
            'updatedBy' => $this->whenLoaded('updatedBy', fn() => new CreatedByResource($this->updatedBy)),
        ];
    }
}
