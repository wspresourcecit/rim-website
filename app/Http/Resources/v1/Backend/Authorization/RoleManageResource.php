<?php

namespace App\Http\Resources\v1\Backend\Authorization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\Backend\CreatedByResource;

class RoleManageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'createdBy' => $this->whenLoaded('createdBy', fn() => new CreatedByResource($this->createdBy)),
            'name' => $this->name,
            'permissions' =>  $this->when($this->permission_id, $this->permission_id),
            'is_active' => $this->when($this->is_active, $this->is_active),
            'permissions_count' => $this->when(
                $this->permissions_count,
                $this->permissions_count
            ),
            'created_at' => $this->when($this->created_at, $this->created_at),
        ];
    }
}
