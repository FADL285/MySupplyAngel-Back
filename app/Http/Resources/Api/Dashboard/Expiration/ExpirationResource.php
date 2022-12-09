<?php

namespace App\Http\Resources\Api\Dashboard\Expiration;

use App\Http\Resources\Api\Dashboard\Category\CategoryResource;
use App\Http\Resources\Api\Dashboard\User\SimpleUserResource;
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
            "id"                => (int) $this->id,
            "user"              => $this->user ? new SimpleUserResource($this->user) : null,
            "title"             => (string) $this->title,
            "desc"              => (string) $this->desc,
            "type"              => (string) $this->type,
            "company_name"      => (string) $this->company_name,
            "product_name"      => (string) $this->product_name,
            "categories"        => CategoryResource::collection($this->categories),
            "expiration_images" => ExpirationMediaResource::collection($this->expiration_images),
            "expiration_files"  => ExpirationMediaResource::collection($this->expiration_files),
            "expiry_date"       => $this->expiry_date ? $this->expiry_date->format("Y-m-d") : null,
            "is_expired"        => $this->expiry_date ? $this->expiry_date <= now() : false,
            'status'            => (string) $this->status,
            "created_at"        => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
