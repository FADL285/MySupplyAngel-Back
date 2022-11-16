<?php

namespace App\Http\Resources\Api\Dashboard\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
        }

        return [
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'slug'       => (string) $this->slug,
            'desc'       => (string) $this->desc,
            'image'      => $this->image,
            'is_active'  => (bool) $this->is_active,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
