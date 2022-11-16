<?php

namespace App\Http\Resources\Api\WebSite\OurServices;

use Illuminate\Http\Resources\Json\JsonResource;

class OurServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'     => (int) $this->id,
            'avatar' => (string) $this->avatar,
            'title'  => (string) $this->title,
            'desc'   => (string) $this->desc,
        ];
    }
}
