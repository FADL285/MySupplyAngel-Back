<?php

namespace App\Http\Resources\Api\Dashboard\Country;

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
        $locales = [];

        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['name']        = $this->translate($locale)->name;
            $locales[$locale]['slug']        = $this->translate($locale)->slug;
            $locales[$locale]['currency']    = $this->translate($locale)->currency;
            $locales[$locale]['nationality'] = $this->translate($locale)->nationality;
        }

        return [
            'id'          => (int) $this->id,
            'name'        => (string) $this->name,
            'slug'        => (string) $this->slug,
            'currency'    => (string) $this->currency,
            'nationality' => (string) $this->nationality,
            'continent'   => (string) $this->continent,
            'phone_code'  => (string) $this->phone_code,
            'short_name'  => (string) $this->short_name,
            'image'       => (string) $this->image,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
