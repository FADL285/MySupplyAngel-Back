<?php

namespace App\Http\Resources\Api\Dashboard\MyClient;

use Illuminate\Http\Resources\Json\JsonResource;

class MyClientResource extends JsonResource
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
            'avatar'     => (string) $this->avatar,
            'name'       => (string) $this->name,
            'comment'    => (string) $this->comment,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
