<?php

namespace App\Http\Resources\Api\Dashboard\Agent;

use App\Http\Resources\Api\Dashboard\Category\CategoryResource;
use App\Http\Resources\Api\Dashboard\User\SimpleUserResource;
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
        return [
            'id'             => (int) $this->id,
            'user'           => $this->user ? new SimpleUserResource($this->user) : null,
            'title'          => (string) $this->title,
            'desc'           => (string) $this->desc,
            'agent_type'     => (string) $this->agent_type,
            'type'           => (string) $this->type,
            'company_name'   => (string) $this->company_name,
            'product_name'   => (string) $this->product_name,

            'categories'     => CategoryResource::collection($this->categories),
            'agent_images'   => AgentMediaResource::collection($this->agent_images),
            'agent_files'    => AgentMediaResource::collection($this->agent_files),

            'expiry_date'    => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'     => $this->expiry_date ? $this->expiry_date <= now() : false,
            'agent_offers'   => $this->when(auth('api')->check() && $this->user_id == auth('api')->id(), AgentOfferResource::collection($this->offers)),
            'status'         => (string) $this->status,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
