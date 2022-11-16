<?php

namespace App\Http\Resources\Api\Dashboard\Agent;

use App\Http\Resources\Api\Dashboard\Category\CategoryResource;
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
            'id'           => (int) $this->id,
            'title'        => (string) $this->title,
            'desc'         => (string) $this->desc,
            'type'         => (string) $this->type,
            'user_id'      => (int) $this->user_id,
            'user_name'    => (string) optional($this->user)->name,
            'phone'        => (string) optional($this->user)->phone,
            'company_name' => (string) $this->company_name,
            'product_name' => (string) $this->product_name,

            'categories'   => CategoryResource::collection($this->categories),
            'agent_images' => AgentMediaResource::collection($this->agent_images),
            'agent_files'  => AgentMediaResource::collection($this->agent_other_files),

            'status'       => (bool) $this->status,
            'expiry_date'  => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'   => $this->expiry_date ? $this->expiry_date <= now() : false,
            'created_at'   => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
