<?php

namespace App\Http\Resources\v1\Backend\Setting;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtensionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_code' => $this->when($this->short_code, $this->short_code),
            'image' => $this->when($this->image, fn() => asset($this->image)),
            'credentials' => $this->when($this->credentials, $this->credentials),
            'instructions' => $this->when($this->instructions, $this->instructions),
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'is_api' => $this->when($this->is_api, $this->is_api),
            'created_at' => $this->when($this->created_at, fn() => $this->created_at?->format('Y-m-d H:i:s')),
        ];
    }
}
