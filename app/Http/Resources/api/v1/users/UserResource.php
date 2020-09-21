<?php

namespace App\Http\Resources\api\v1\users;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d\TH:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d\TH:i:s'),
        ];
    }
}
