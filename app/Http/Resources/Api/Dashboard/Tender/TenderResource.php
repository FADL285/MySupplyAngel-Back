<?php

namespace App\Http\Resources\Api\Dashboard\Tender;

use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
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
            'id'                          => (int) $this->id,
            'title'                       => (string) $this->title,
            'desc'                        => (string) $this->desc,
            'user_id'                     => (int) $this->user_id.
            'user_name'                   => (string) optional($this->user)->name,
            'phone'                       => (string) optional($this->user)->phone,
            'categories'                  => $this->categories,
            'tender_images'               => TenderMediaResource::collection($this->tender_images),
            'tender_other_files'          => TenderMediaResource::collection($this->tender_other_files),
            'status'                      => (bool) $this->status,
            'expiry_date'                 => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'is_expired'                  => $this->expiry_date ? $this->expiry_date <= now() : false,
            'insurance_value'             => (double) $this->insurance_value,
            'created_at'                  => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'tender_specifications_value' => (double) $this->tender_specifications_value,
            'tender_specifications_file'  => $this->tender_specifications_file ? new TenderMediaResource($this->tender_specifications_file) : null,
        ];
    }
}
