<?php

namespace App\Http\Controllers\api\v1\worktimeentries;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\worktimeentries\GetWorktimeEntriesRequest;
use App\Http\Requests\api\v1\worktimeentries\SaveWorktimeEntryRequest;
use App\Http\Resources\api\v1\worktimeentries\WorktimeEntryResource;
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

        $startedAt = $request->query('started_at', $request->started_at);
        $endedAt = $request->query('ended_at', $request->ended_at);

        $startedAt = $startedAt
            ? Carbon::createFromFormat('Y-m-d', $startedAt)->startOfDay()
            : Carbon::now()->addDays(-14);

        $endedAt = $endedAt
            ? Carbon::createFromFormat('Y-m-d', $endedAt)->endOfDay()
            : Carbon::now();

        if ($user->hasRole('admin')) {
            $worktimeEntries = WorktimeEntry::where(function($query) use($startedAt, $endedAt) {
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
                        ['ended_at', '<=', $endedAt]
                    ]);
                });
        }

        return response([
            'worktime_entries' => WorktimeEntryResource::collection($worktimeEntries->get())
        ]);
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
            ['user_id' => $user->id]
        ));

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
        $worktimeEntry = WorktimeEntry::find($id);

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
        $worktimeEntry = WorktimeEntry::find($id);

        if ((empty($worktimeEntry) || $worktimeEntry->user_id != Auth::user()->id)
            && !Auth::user()->hasRole('admin'))
        {
            return response()->json(['message' => 'Not Found.'], 404);
        }

        $worktimeEntry->forceFill(array_merge(
            $request->validated(),
            ['user_id' => Auth::user()->id]
        ));

        $worktimeEntry->save();

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
        $worktimeEntry = WorktimeEntry::find($id);

        if ((empty($worktimeEntry) || $worktimeEntry->user_id != Auth::user()->id)
            && !Auth::user()->hasRole('admin'))
        {
            return response()->json(['message' => 'Not Found.'], 404);
        }

        $worktimeEntry->delete();

        return response()->json();
    }
}
