<?php

namespace App\Http\Resources\Api\Dashboard\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->data;

        if (isset($data['title']) && is_array($data['title']) && isset($data['body']) && is_array($data['body'])) {
            $data['title']  = $data['title'][app()->getLocale()];
            $data['body']   = $data['body'][app()->getLocale()];
            $data['sender'] = isset($this->data['sender']) ? json_decode($this->data['sender'], true) : null;
        }

        $return_data =  [
            'id'           => $this->id,
            'created_time' => $this->created_at->diffforHumans(),
            'created_at'   => date('d/m/Y', strtotime($this->created_at)),
            'read_at'      => $this->read_at,
            'is_readed'    => $this->read_at ? true : false,
        ] + $data;

        return $return_data;
    }
}
