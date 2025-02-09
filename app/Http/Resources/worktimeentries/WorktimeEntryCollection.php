<?php

namespace App\Http\Resources\worktimeentries;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WorktimeEntryCollection extends ResourceCollection
{
    public $collects = WorktimeEntryResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
