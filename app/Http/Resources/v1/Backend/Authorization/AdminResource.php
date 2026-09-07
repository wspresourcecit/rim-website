<?php

namespace App\Http\Resources\v1\Backend\Authorization;

use App\Http\Resources\v1\Backend\CreatedByResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'email' => $this->when($this->email, $this->email),
            'role_id' => $this->when($this->role_id, $this->role_id),
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMedia('image')?->url),
            'role' => $this->whenLoaded('role', fn() => new RoleManageResource($this->role)),
            'createdBy' => $this->whenLoaded('createdBy', fn() => new CreatedByResource($this->createdBy)),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'deleteable' => $this->when(isset($this->deleteable), $this->deleteable),
            'created_at' => $this->when($this->created_at, fn() => $this->created_at->format('Y-m-d H:i:s')),
        ];
    }
}
