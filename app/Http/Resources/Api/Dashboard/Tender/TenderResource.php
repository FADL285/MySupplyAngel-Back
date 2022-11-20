<?php

namespace App\Http\Resources\Api\Dashboard\Tender;

use App\Http\Resources\Api\Dashboard\Category\CategoryResource;
use App\Http\Resources\Api\Dashboard\User\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
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
            'id'                          => (int) $this->id,
            'title'                       => (string) $this->title,
            'desc'                        => (string) $this->desc,
            'user'                        => $this->user ? new SimpleUserResource($this->user) : null,
            'company_name'                => (string) $this->company_name,
            'categories'                  => CategoryResource::collection($this->categories),
            'tender_images'               => TenderMediaResource::collection($this->tender_images),
            'tender_other_files'          => TenderMediaResource::collection($this->tender_other_files),
            'expiry_date'                 => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'                  => $this->expiry_date ? $this->expiry_date <= now() : false,
            'insurance_value'             => (double) $this->insurance_value,
            'tender_specifications_value' => (double) $this->tender_specifications_value,
            'tender_specifications_file'  => $this->tender_specifications_file ? new TenderMediaResource($this->tender_specifications_file) : null,
            'tender_offers'               => TenderOfferResource::collection($this->offers),
            'created_at'                  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
