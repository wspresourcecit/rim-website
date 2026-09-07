<?php

namespace App\Http\Resources\v1\Backend\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteAssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'footer_text' => $this->when($this->footer_text, $this->footer_text),
            'e_tin' => $this->when($this->e_tin, $this->e_tin),
            'trade_license' =>  $this->when($this->trade_license, $this->trade_license),
            'number' => $this->when($this->number, $this->number),
            'email' =>  $this->when($this->email, $this->email),
            'address' =>  $this->when($this->address, $this->address),
            'map_title' =>  $this->when($this->map_title, $this->map_title),
            'timezone' => $this->when($this->timezone, $this->timezone),
            'map' =>  $this->when($this->map, $this->map),
            'copyright_text' =>  $this->when($this->copyright_text, $this->copyright_text),
            'favicon' => $this->when($this->favicon, $this->favicon ? asset($this->favicon) : null),
            'main_logo' => $this->when($this->main_logo, $this->main_logo ? asset($this->main_logo) : null),
            'dark_logo' => $this->when($this->dark_logo, $this->dark_logo ? asset($this->dark_logo) : null),
            'social_handles' => $this->when($this->social_handles, json_decode($this->social_handles, true)),
            'fb_page_follower' => $this->when($this->fb_page_follower, $this->fb_page_follower),
            'youtube_subscriber' => $this->when($this->youtube_subscriber, $this->youtube_subscriber),
            'fb_group_follower' => $this->when($this->fb_group_follower, $this->fb_group_follower),
            'maintenance_mode' => $this->when(isset($this->maintenance_mode), $this->maintenance_mode),
            'maintenance_message' => $this->when($this->maintenance_message, $this->maintenance_message),

            'floating_facebook_link' => $this->when($this->floating_facebook_link, $this->floating_facebook_link),
            'floating_instagram_link' => $this->when($this->floating_youtube_link, $this->floating_youtube_link),
            'floating_whatsapp_number' => $this->when($this->floating_whatsapp_number, $this->floating_whatsapp_number),
        ];
    }
}
