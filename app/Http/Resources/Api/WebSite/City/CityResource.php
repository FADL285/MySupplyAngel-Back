<?php

namespace App\Http\Resources\Api\WebSite\City;

use App\Http\Resources\Api\WebSite\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'id'          => (int) $this->id,
            'name'        => (string) $this->name,
            'slug'        => (string) $this->slug,
            'country'     => $this->country ? new CountryResource($this->country) : null,
            'short_name'  => (string) $this->short_name,
            'postal_code' => (int) $this->postal_code,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
