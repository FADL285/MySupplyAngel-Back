<?php

namespace App\Http\Resources\Api\WebSite\User;

use Illuminate\Http\Resources\Json\JsonResource;

class SenderResource extends JsonResource
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
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'phone_code' => (string) $this->phone_code,
            'phone'      => (string) $this->phone,
            'avatar'     => (string) $this->avatar,
        ];
    }
}
