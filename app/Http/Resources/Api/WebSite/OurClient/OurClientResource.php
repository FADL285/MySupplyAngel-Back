<?php

namespace App\Http\Resources\Api\WebSite\OurClient;

use Illuminate\Http\Resources\Json\JsonResource;

class OurClientResource extends JsonResource
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
            'id'      => (int) $this->id,
            'avatar'  => (string) $this->avatar,
            'name'    => (string) $this->name,
            'comment' => (string) $this->comment,
        ];
    }
}
