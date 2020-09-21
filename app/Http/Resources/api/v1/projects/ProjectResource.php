<?php

namespace App\Http\Resources\api\v1\projects;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $project = [
            'id' => $this->id,
            'name' => $this->name,
            'founder' => [
                'name' => $this->founder->name,
                'email' => $this->founder->email
            ],
            'project_manager' => [
                'name' => $this->projectManager->name,
                'email' => $this->projectManager->email
            ],
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d\TH:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d\TH:i:s'),
        ];

        if (!Auth::user()->hasRole('admin')) {
            $project['is_personal'] = $this->is_personal;
        }

        return $project;
    }
}
