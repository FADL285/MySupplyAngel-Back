<?php

namespace App\Http\Resources\Api\Dashboard\City;

use App\Http\Resources\Api\Dashboard\Country\SimpleCountryResource;
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
        $locales = [];

        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['name'] = $this->translate($locale)->name;
            $locales[$locale]['slug'] = $this->translate($locale)->slug;
        }

        return [
            'id'          => (int) $this->id,
            'country'     => $this->country ? new SimpleCountryResource($this->country) : null,
            'short_name'  => (string) $this->short_name,
            'postal_code' => (int) $this->postal_code,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
