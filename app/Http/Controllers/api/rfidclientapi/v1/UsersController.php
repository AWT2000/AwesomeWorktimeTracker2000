<?php

namespace App\Http\Controllers\api\rfidclientapi\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\users\UserWithLastEntryResource;
use App\Models\RfidTag;
use App\Models\WorktimeEntry;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request, string $rfidTagString)
    {
        $rfidTag = RfidTag::where('tag', $rfidTagString)
            ->with(['users'])
            ->first();

        if (!$rfidTag) {
            return response()->json(['message' => 'Not Found.'], 404);
        }

        if ($user = $rfidTag->users()->first()) {
            $currentEntry = $user->worktimeEntriesWithoutEnding()->first();

            if ($currentEntry) {
                $user->lastEntry = $currentEntry;
            } else {
                $user->lastEntry = WorktimeEntry::where([
                        ['user_id', '=',$user->id]
                    ])
                    ->orderBy('ended_at', 'DESC')
                    ->first();
            }

            return response()->json(UserWithLastEntryResource::make($user));
        }

        return response()->json(['message' => 'Not Found.'], 404);
    }
}
