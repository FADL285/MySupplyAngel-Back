<?php

namespace App\Http\Resources\Api\WebSite\Country;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'nationality' => $this->nationality,
            'currency'    => $this->currency,
            'slug'        => $this->slug,
            'continent'   => $this->continent,
            'phone_code'  => $this->phone_code,
            'short_name'  => $this->short_name,
            'image'       => $this->image,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
