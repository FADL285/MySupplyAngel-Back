<?php

namespace App\Http\Resources\Api\Dashboard\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'id'                => (int) $this->id,
            'title'             => (string) $this->title,
            'desc'              => (string) $this->desc,
            'note'              => (string) $this->note,
            'price'             => (double) $this->price,
            'duration_by_month' => (int) $this->duration_by_month,
            'type'              => (string) $this->type,
        ];
    }
}
