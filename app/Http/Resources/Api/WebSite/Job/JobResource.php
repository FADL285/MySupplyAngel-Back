<?php

namespace App\Http\Resources\Api\WebSite\Job;

use App\Http\Resources\Api\WebSite\City\CityResource;
use App\Http\Resources\Api\WebSite\Country\CountryResource;
use App\Http\Resources\Api\WebSite\User\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            "id"           => (int) $this->id,
            "job_title"    => (string) $this->job_title,
            "company_name" => (string) $this->company_name,
            "desc"         => (string) $this->desc,
            "city"         => $this->city ? new CityResource($this->city) : null,
            "country"      => $this->country ? new CountryResource($this->country) : null,
            "i_applied"    => auth('api')->check() && $this->user_id != auth('api')->id() && in_array(auth('api')->id(), $this->users()->pluck('user_id')->toArray()) ? true : false,
            "job_applications" => auth('api')->check() && $this->user_id == auth('api')->id() ? SimpleUserResource::collection($this->users) : [],
        ];
    }
}
