<?php

namespace App\Http\Resources\Api\WebSite\Agent;

use App\Http\Resources\Api\WebSite\User\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentOfferResource extends JsonResource
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
            'images' => AgentMediaResource::collection($this->tender_offer_images),
            'files'  => AgentMediaResource::collection($this->tender_offer_files),
        ];
    }
}
