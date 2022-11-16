<?php

namespace App\Http\Resources\Api\Dashboard\Setting;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $settings = Setting::latest()->get();
        $data = [];

        foreach($settings as $k)
        {
            $data[$k['key']] = $k['value'];
        }

        return $data;
    }
}
