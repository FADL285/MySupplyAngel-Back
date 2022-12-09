<?php

namespace App\Http\Resources\Api\WebSite\User;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleUserResource extends JsonResource
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
            'id'          => (int) $this->id,
            'name'        => (string) $this->name,
            'phone_code'  => (string) $this->phone_code,
            'phone'       => (string) $this->phone,
            'whats'       => (string) $this->whats,
            'email'       => (string) $this->email,
            'address'     => (string) $this->address,
            'is_need_job' => (bool) $this->is_need_job,
        ];
    }
}
