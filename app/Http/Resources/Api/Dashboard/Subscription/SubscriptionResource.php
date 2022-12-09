<?php

namespace App\Http\Resources\Api\Dashboard\Subscription;

use App\Http\Resources\Api\Dashboard\Package\PackageResource;
use App\Http\Resources\Api\Dashboard\User\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'id'             => (int) $this->id,
            'user'           => $this->user ? new SimpleUserResource($this->user) : null,
            'package'        => $this->package ? new PackageResource($this->package) : null,
            'start_at'       => $this->start_at,
            'end_at'         => $this->end_at,
            'transaction_id' => $this->transaction_id,
            'status'         => (string) $this->status,
        ];
    }
}
