<?php

namespace App\Http\Controllers\api\v1\worktimeentries;

use App\Actions\AttachCollidingWorktimeEntriesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\worktimeentries\GetWorktimeEntriesRequest;
use App\Http\Requests\api\v1\worktimeentries\SaveWorktimeEntryRequest;
use App\Http\Resources\worktimeentries\WorktimeEntryCollection;
use App\Http\Resources\worktimeentries\WorktimeEntryResource;
use App\Models\WorktimeEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WorktimeEntriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(GetWorktimeEntriesRequest $request)
    {
        $user = $request->user();

        $startedAt = $request->started_at;
        $endedAt = $request->ended_at;

        if ($user->hasRole('admin')) {
            $worktimeEntries = WorktimeEntry::where(
                function($query) use($startedAt, $endedAt) {
                    $query->where([
                        ['started_at', '>=', $startedAt],
                        ['ended_at', '<=', $endedAt]
                    ]);
                });
        } else {
            $worktimeEntries = WorktimeEntry::where('user_id', $user->id)
                ->where(function($query) use($startedAt, $endedAt) {
                    $query->where([
                        ['started_at', '>=', $startedAt],
                        ['ended_at', '!=', null],
                        ['ended_at', '<=', $endedAt]
                    ]);
                });
        }
        return new WorktimeEntryCollection(
            $worktimeEntries->paginate(30)->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\api\v1\worktimeentries\SaveWorktimeEntryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveWorktimeEntryRequest $request)
    {
        $user = Auth::user();

        $worktimeEntry = WorktimeEntry::create(array_merge(
            $request->validated(),
            [
                'user_id' => $user->id,
                'started_at' => Carbon::parse(
                    $request->validated()['started_at'])->setTimezone('UTC'),
                'ended_at' => Carbon::parse(
                    $request->validated()['ended_at'])->setTimezone('UTC')
            ]
        ));

        (new AttachCollidingWorktimeEntriesAction($worktimeEntry, $user->id))->execute();

        return response(WorktimeEntryResource::make($worktimeEntry));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $worktimeEntry = WorktimeEntry::findOrFail($id);

        if ((empty($worktimeEntry) || $worktimeEntry->user_id != Auth::user()->id)
            && !Auth::user()->hasRole('admin'))
        {
            return response()->json(['message' => 'Not Found.'], 404);
        }
        return response(WorktimeEntryResource::make($worktimeEntry));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\api\v1\worktimeentries\SaveWorktimeEntryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveWorktimeEntryRequest $request, $id)
    {
        $worktimeEntry = WorktimeEntry::findOrFail($id);

        $user = Auth::user();

        if ((empty($worktimeEntry) || $worktimeEntry->user_id != $user->id)
            && !$user->hasRole('admin'))
        {
            return response()->json(['message' => 'Not Found.'], 404);
        }

        $worktimeEntry->forceFill(array_merge(
            $request->validated(),
            [
                'user_id' => $user->id,
                'started_at' => Carbon::parse(
                    $request->validated()['started_at'])->setTimezone('UTC'),
                'ended_at' => Carbon::parse(
                    $request->validated()['ended_at'])->setTimezone('UTC')
            ]
        ));

        $worktimeEntry->save();

        (new AttachCollidingWorktimeEntriesAction($worktimeEntry, $user->id))->execute();

        return response(WorktimeEntryResource::make($worktimeEntry));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $worktimeEntry = WorktimeEntry::findOrFail($id);

        if ((empty($worktimeEntry) || $worktimeEntry->user_id != Auth::user()->id)
            && !Auth::user()->hasRole('admin'))
        {
            return response()->json(['message' => 'Not Found.'], 404);
        }

        $worktimeEntry->collidingEntries()->detach();

        $worktimeEntry->delete();

        return response()->json();
    }
}
