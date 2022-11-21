<?php

namespace App\Http\Resources\Api\Dashboard\Contact;

use App\Http\Resources\Api\Dashboard\User\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'id'         => $this->id,
            'frist_name' => $this->frist_name,
            'last_name'  => $this->last_name,
            'type'       => $this->type,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'title'      => $this->title,
            'content'    => $this->content,
            'read_at'    => $this->read_at,
            'user'       => SimpleUserResource::make($this->user),
            // 'replies'    => $this->replies->count() > 0 ? ContactReplyResource::collection($this->replies) : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
