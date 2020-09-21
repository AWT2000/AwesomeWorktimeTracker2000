<?php

namespace App\Rules\worktimeentries;

use App\Models\WorktimeEntry;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DateRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $collapsingEntry = WorktimeEntry::where('user_id', Auth::user()->id);

        if ($attribute == 'started_at') {
            $collapsingEntry->where(function($query) use($value) {
                    $query->where([
                        ['started_at', '<=', $value],
                        ['ended_at', '>', $value]
                    ]);
                })->orWhere(function($query) use($value) {
                    $query->where([
                        ['started_at', '<=', $value],
                        ['ended_at', '=', null]
                    ]);
                });
        } else {
            $collapsingEntry->where(function($query) use($value) {
                    $query->where([
                        ['started_at', '<', $value],
                        ['ended_at', '>', $value]
                    ]);
                })->orWhere(function($query) use($value) {
                    $query->where([
                        ['started_at', '<', $value],
                        ['ended_at', '=', null]
                    ]);
                });
        }
        if (! empty($collapsingEntry->first())) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute must not collide with other worktime entries.';
    }
}
