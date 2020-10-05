<?php

namespace App\Rules\worktimeentries;

use App\Models\WorktimeEntry;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DateRule implements Rule
{
    /**
     * Id of self.
     *
     * @var integer
     */
    private $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id = null)
    {
        if ($id) {
            $this->id = $id;
        }
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
        $collidingEntry = WorktimeEntry::where('user_id', Auth::user()->id);

        if ($this->id) {
            $collidingEntry->where('id', '<>', $this->id);
        }

        if ($attribute == 'started_at') {
            $collidingEntry = $this->getQueryForStartedAtAttributeValidation(
                $value,
                $collidingEntry);
        } else {
            $collidingEntry = $this->getQueryForEndedAtAttributeValidation(
                $value,
                $collidingEntry);
        }
        if (! empty($collidingEntry->first())) {
            return false;
        }
        return true;
    }

    /**
     * Get query for colliding entries for started_at attribute
     */
    private function getQueryForStartedAtAttributeValidation($value, $collidingEntry) {
        return $collidingEntry->where(function($query) use($value) {
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
    }

    /**
     * Get query for colliding entries for ended_at attribute
     */
    private function getQueryForEndedAtAttributeValidation($value, $collidingEntry) {
        return $collidingEntry->where(function($query) use($value) {
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
