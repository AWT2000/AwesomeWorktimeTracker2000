<?php

namespace App\Http\Resources\users;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserWithLastEntryResource extends JsonResource
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
            'last_entry' => $this->lastEntry != null
                ? [
                    'id' => $this->lastEntry->id,
                    "started_at" => Carbon::parse($this->lastEntry->started_at)->toIso8601String(),
                    "ended_at" => $this->lastEntry->ended_at != null
                        ? Carbon::parse($this->lastEntry->ended_at)->toIso8601String()
                        : null,
                ]
                : null
        ];
    }
}
