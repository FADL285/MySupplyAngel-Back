<?php

namespace App\Http\Resources\Api\WebSite\Tender;

use App\Http\Resources\Api\WebSite\User\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderOfferResource extends JsonResource
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
            'id'     => (int) $this->id,
            'user'   => $this->user ? new SimpleUserResource($this->user) : null,
            'desc'   => (string) $this->desc,
            'images' => TenderMediaResource::collection($this->tender_offer_images),
            'files'  => TenderMediaResource::collection($this->tender_offer_files),
        ];
    }
}
