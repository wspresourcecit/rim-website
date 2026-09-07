<?php

namespace App\Http\Resources\v1\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerCompanyResource extends JsonResource
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
            'logo' => $this->when($this->logo, fn () => asset($this->logo)),
            'type' => $this->when($this->type, $this->type),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at->format('Y-m-d H:i:s')),
            'createdBy' => $this->whenLoaded('createdBy', fn () => new CreatedByResource($this->createdBy)),
            'updatedBy' => $this->whenLoaded('updatedBy', fn () => new CreatedByResource($this->updatedBy)),
        ];
    }
}
