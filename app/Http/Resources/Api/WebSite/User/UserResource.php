<?php

namespace App\Http\Resources\Api\WebSite\User;

use App\Http\Resources\Api\WebSite\Category\CategoryResource;
use App\Http\Resources\Api\WebSite\City\CityResource;
use App\Http\Resources\Api\WebSite\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'                      => (int) $this->id,
            'avatar'                  => (string) $this->avatar,
            'name'                    => (string) $this->name,
            'phone_code'              => (string) $this->phone_code,
            'phone'                   => (string) $this->phone,
            'whats'                   => (string) $this->whats,
            'email'                   => (string) $this->email,
            'address'                 => (string) $this->address,
            'company_name'            => (string) optional($this->company)->company_name,
            'commercial_register_num' => (string) optional($this->company)->commercial_register_num,
            'tax_card_num'            => (string) optional($this->company)->tax_card_num,
            'country'                 => optional($this->profile)->country_id ? new CountryResource($this->profile->country) : null,
            'city'                    => optional($this->profile)->city_id ? new CityResource($this->profile->city) : null,
            'categories'              => CategoryResource::collection($this->categories),
            'is_need_job'             => (bool) $this->is_need_job,
            'previous_work'           => $this->previous_work,
            'is_subcribed'            => (bool) false,
            'token'                   => $this->when($this->token, $this->token),
        ];
    }
}
