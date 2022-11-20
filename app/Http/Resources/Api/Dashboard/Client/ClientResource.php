<?php

namespace App\Http\Resources\Api\Dashboard\Client;

use App\Http\Resources\Api\Dashboard\City\CityResource;
use App\Http\Resources\Api\Dashboard\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'id'                   => (int) $this->id,
            'name'                 => (string) $this->name,
            'phone_code'           => (string) $this->phone_code,
            'phone'                => (string) $this->phone,
            'email'                => (string) $this->email,
            'avatar'               => (string) $this->avatar,
            'country'              => optional($this->profile)->country_id ? new CountryResource($this->profile->country) : null,
            'city'                 => optional($this->profile)->city_id ? new CityResource($this->profile->city) : null,
            'is_admin_active_user' => (bool) $this->is_admin_active_user,
            'is_active'            => (bool) $this->is_active,
            'gender'               => (string) $this->gender,
            'is_ban'               => (bool)$this->is_ban,
            'ban_reason'           => (string) $this->ban_reason,
            'wallet'               => (float) optional($this->wallet)->balance ?? 0.0,
            // 'wallet_transations'   => WalletTransactionResource::collection($this->walletTransactions),
            // 'Address'              => AddressResource::collection($this->address),
            'created_at'           => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
