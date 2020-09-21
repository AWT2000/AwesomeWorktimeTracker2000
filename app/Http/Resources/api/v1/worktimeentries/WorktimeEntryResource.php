<?php

namespace App\Http\Resources\api\v1\worktimeentries;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class WorktimeEntryResource extends JsonResource
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
            "id" => $this->id,
            "project_id" => $this->project_id,
            "started_at" => Carbon::parse($this->started_at)->format('Y-m-d\TH:i:s'),
            "ended_at" => Carbon::parse($this->ended_at)->format('Y-m-d\TH:i:s'),
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d\TH:i:s'),
            "updated_at" => Carbon::parse($this->updated_at)->format('Y-m-d\TH:i:s'),
        ];
    }
}
