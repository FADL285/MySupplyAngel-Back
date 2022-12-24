<?php

namespace App\Http\Resources\Api\WebSite\Agent;

use App\Http\Resources\Api\WebSite\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
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
            'id'             => (int) $this->id,
            'title'          => (string) $this->title,
            'desc'           => (string) $this->desc,
            'agent_type'     => (string) $this->agent_type,
            'type'           => (string) $this->type,
            'user_name'      => (string) optional($this->user)->name,
            'phone'          => (string) optional($this->user)->phone,
            'company_name'   => (string) $this->company_name,
            'product_name'   => (string) $this->product_name,

            'categories'     => CategoryResource::collection($this->categories),
            'agent_images'   => AgentMediaResource::collection($this->agent_images),
            'agent_files'    => AgentMediaResource::collection($this->agent_files),

            'is_favorite'    => auth('api')->check() && auth('api')->user()->agentFavorites()->where('agent_id', $this->id)->first() ? true : false,
            'expiry_date'    => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'     => $this->expiry_date ? $this->expiry_date <= now() : false,
            'my_agent_offer' => auth('api')->check() && $this->user_id != auth('api')->id() && isset($my_offer) ? new AgentOfferResource($my_offer) : null,
            'agent_offers'   => auth('api')->check() && $this->user_id == auth('api')->id() ? AgentOfferResource::collection($this->offers) : [],
            'status'         => (string) $this->status,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
