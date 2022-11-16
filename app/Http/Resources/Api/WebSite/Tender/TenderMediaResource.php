<?php

namespace App\Http\Resources\Api\WebSite\Tender;

use Illuminate\Http\Resources\Json\JsonResource;

class TenderMediaResource extends JsonResource
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
            'id'    => (int) $this->id,
            'media' => (string) asset('storage/images/'.$this->media)
        ];
    }
}
