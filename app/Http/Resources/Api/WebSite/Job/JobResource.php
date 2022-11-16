<?php

namespace App\Http\Resources\Api\WebSite\Job;

use App\Http\Resources\Api\WebSite\City\CityResource;
use App\Http\Resources\Api\WebSite\Country\CountryResource;
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
        ];
    }
}
