<?php

namespace App\Http\Controllers\api\rfidclientapi\v1;

use App\Actions\AttachCollidingWorktimeEntriesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\worktimeentries\SaveWorktimeEntryRequest;
use App\Http\Resources\worktimeentries\WorktimeEntryResource;
use App\Models\RfidTag;
use App\Models\WorktimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WorktimeEntryController extends Controller
{
    public function store(SaveWorktimeEntryRequest $request)
    {
        if (!$request['rfid_tag']) {
            return response('', 400);
        }

        $rfidTag = RfidTag::where('tag', $request['rfid_tag'])
            ->with(['users'])
            ->first();

        if (!$rfidTag) {
            return response('', 422);
        }

        if ($user = $rfidTag->users()->first()) {
            $worktimeEntry = WorktimeEntry::create(array_merge(
                $request->validated(),
                [
                    'user_id' => $user->id,
                    'started_at' => Carbon::parse(
                        $request->validated()['started_at'])->setTimezone('UTC'),
                    'ended_at' => !empty($request->validated()['ended_at'])
                        ? Carbon::parse(
                            $request->validated()['ended_at'])->setTimezone('UTC')
                        : null
                ]
            ));


            if (!empty($request->validated()['ended_at'])) {
                (new AttachCollidingWorktimeEntriesAction($worktimeEntry, $user->id))->execute();
            }

            return response(WorktimeEntryResource::make($worktimeEntry));
        }

        return response('', 422);
    }

    public function update(SaveWorktimeEntryRequest $request, $id)
    {
        $worktimeEntry = WorktimeEntry::findOrFail($id);

        if (!$request['rfid_tag']) {
            return response('', 400);
        }

        $rfidTag = RfidTag::where('tag', $request['rfid_tag'])
            ->with(['users'])
            ->first();

        if (!$rfidTag) {
            return response('', 422);
        }

        if ($user = $rfidTag->users()->first()) {

            if ($worktimeEntry->user_id != $user->id) {
                return response()->json(['message' => 'Not Found.'], 404);
            }

            $worktimeEntry->forceFill(array_merge(
                $request->validated(),
                [
                    'user_id' => $user->id,
                    'started_at' => Carbon::parse(
                        $request->validated()['started_at'])->setTimezone('UTC'),
                    'ended_at' => !empty($request->validated()['ended_at'])
                        ? Carbon::parse(
                            $request->validated()['ended_at'])->setTimezone('UTC')
                        : null
                ]
            ));

            $worktimeEntry->save();

            (new AttachCollidingWorktimeEntriesAction($worktimeEntry, $user->id))->execute();

            return response(WorktimeEntryResource::make($worktimeEntry));
        }

        return response('', 422);
    }
}
