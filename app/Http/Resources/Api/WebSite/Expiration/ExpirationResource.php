<?php

namespace App\Http\Resources\Api\WebSite\Expiration;

use App\Http\Resources\Api\WebSite\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpirationResource extends JsonResource
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
            'type'              => (string) $this->type,
            'company_name'      => (string) $this->company_name,
            'product_name'      => (string) $this->product_name,
            'user_name'         => (string) optional($this->user)->name,
            'phone'             => (string) optional($this->user)->phone,
            'categories'        => CategoryResource::collection($this->categories),
            'expiration_images' => ExpirationMediaResource::collection($this->expiration_images),
            'expiration_files'  => ExpirationMediaResource::collection($this->expiration_files),
            'is_favorite'       => auth('api')->check() && auth('api')->user()->expirationsFavorite()->where('expiration_id', $this->id)->first() ? true : false,
            'expiry_date'       => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'        => $this->expiry_date ? $this->expiry_date <= now() : false,
            'created_at'        => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
