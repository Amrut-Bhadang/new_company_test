<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $device_type = $request->device_type ?? null;
        $device_token = $request->device_token ?? null;

        if ($this->devices && isset($this->devices[0])) {
            $device_type = $this->devices[0]->device_type;
            $device_token = $this->devices[0]->device_token;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country_code' => $this->country_code,
            'mobile' => $this->mobile,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'is_profile_updated' => $this->is_profile_updated,
            'image' => $this->image,
            'image_type' => $this->image_type,
            'email' => $this->email,
            'device_type' => $device_type,
            'device_token' => $device_token,
            'created_at' => $this->created_at,
            'social_type' => $this->social_type,
        ];
    }
}
