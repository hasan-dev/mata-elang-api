<?php

namespace App\Http\Resources;

;

use App\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'province' => $this->province,
            'city' => $this->city,
            'phone_number'  => $this->phone,
            'oinkcode' => $this->oinkcode,
            'website' => $this->website,
            // 'parent_organization' => Organization::find($this->parent_id), 
        ];
    }
}
