<?php

namespace App\Http\Resources\Api\Dashboard\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'avatar'        => @$this->avatar,
            'phone'         => (int) $this->phone,
            'whats'         => (int) $this->whats,
            'email'         => $this->email,
            'user_type'     => $this->user_type,
            'gender'        => $this->gender,
            'is_active'     => (bool) $this->is_active,
            'is_ban'        => (bool) $this->is_ban,
            'ban_reason'    => $this->ban_reason,
            // 'country'       => CountrySimpleResource::make($this->country),
            // 'city'          => CitySimpleResource::make($this->city),
            'token'         => $this->when($this->token, $this->token),
            'created_at'    => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
