<?php

namespace App\Http\Resources\Api\WebSite\Tender;

use App\Http\Resources\Api\WebSite\Category\CategoryResource;
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
        if (auth('api')->check())
        {
            $my_offer = $this->offers()->where('user_id', auth('api')->id())->first();
        }

        return [
            'id'                          => (int) $this->id,
            'title'                       => (string) $this->title,
            'desc'                        => (string) $this->desc,
            'company_name'                => (string) $this->company_name,
            'user_name'                   => (string) optional($this->user)->name,
            'phone'                       => (string) optional($this->user)->phone,
            'categories'                  => CategoryResource::collection($this->categories),
            'tender_images'               => TenderMediaResource::collection($this->tender_images),
            'tender_other_files'          => TenderMediaResource::collection($this->tender_other_files),
            'is_favorite'                 => auth('api')->check() && auth('api')->user()->tendersFavorite()->where('tender_id', $this->id)->first() ? true : false,
            'expiry_date'                 => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'                  => $this->expiry_date ? $this->expiry_date <= now() : false,
            'insurance_value'             => (double) $this->insurance_value,
            'created_at'                  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'tender_specifications_value' => (double) $this->tender_specifications_value,
            'tender_specifications_file'  => $this->tender_specifications_file ? new TenderMediaResource($this->tender_specifications_file) : null,
            'my_tender_offer'             => auth('api')->check() && $this->user_id != auth('api')->id() && isset($my_offer) ? new TenderOfferResource($my_offer) : null,
            'status'                      => (string) $this->status,
            'tender_offers'               => auth('api')->check() && $this->user_id == auth('api')->id() ? TenderOfferResource::collection($this->offers) : [],

            'added_offer'                 => (bool) isset($my_offer),
            'is_my_agent'                 => auth('api')->check() && $this->user_id == auth('api')->id() ? true : false ,
        ];
    }
}
