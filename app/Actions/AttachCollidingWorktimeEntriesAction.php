<?php

namespace App\Actions;

use App\Models\WorktimeEntry;

class AttachCollidingWorktimeEntriesAction
{
    /**
     * Saved work time entry.
     *
     * @var \App\Models\WorktimeEntry
     */
    private $worktimeEntry;

    /**
     * User id.
     *
     * @var int
     */
    private $userId;

    /**
     * Construct the action.
     *
     * @param \App\Models\WorktimeEntry $worktimeEntry saved entry
     * @param integer $userId user id
     */
    public function __construct(WorktimeEntry $worktimeEntry, int $userId)
    {
        $this->worktimeEntry = $worktimeEntry;
        $this->userId = $userId;
    }

    /**
     * Executes the action.
     *
     * @return void
     */
    public function execute()
    {
        $this->worktimeEntry->collidingEntries()->detach();

        $collidingEntries = WorktimeEntry::where([
            ['user_id', '=', $this->userId],
            ['id', '!=', $this->worktimeEntry->id]
        ]);

        $collidingEntries->where(function($query) {
            $this->getQueryForStartedAtAttribute($query);
            $this->getQueryForEndedAtAttribute($query);
            $this->getQueryForEntriesBetween($query);
        });

        $collidingEntries->get()->each(function($collidingEntry) {
            $this->worktimeEntry->collidingEntries()->attach($collidingEntry);
        });
    }

    /**
     * Get query for colliding entries for started_at attribute
     *
     * @param string $value
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getQueryForStartedAtAttribute($queryBuilder) {
        return $queryBuilder->orWhere(function($query) {
            $query->where([
                ['started_at', '<=', $this->worktimeEntry->started_at],
                ['ended_at', '>', $this->worktimeEntry->started_at]
            ]);
        })->orWhere(function($query) {
            $query->where([
                ['started_at', '<=', $this->worktimeEntry->started_at],
                ['ended_at', '=', null]
            ]);
        });
    }

    /**
     * Get query for colliding entries for ended_at attribute
     *
     * @param string $value
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getQueryForEndedAtAttribute($queryBuilder) {
        return $queryBuilder->orWhere(function($query) {
            $query->where([
                ['started_at', '<', $this->worktimeEntry->ended_at],
                ['ended_at', '>', $this->worktimeEntry->ended_at]
            ]);
        })->orWhere(function($query) {
            $query->where([
                ['started_at', '<', $this->worktimeEntry->ended_at],
                ['ended_at', '=', null]
            ]);
        });
    }

    /**
     * Get query for colliding entries that start before saved entry starts and
     * ends after saved entry ends (saved entry is within) or start after saved
     * entry and end before saved entry ends (colliding entries are withing
     * saved entry).
     *
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getQueryForEntriesBetween($queryBuilder)
    {
        return $queryBuilder->orWhere([
            ['started_at', '>=', $this->worktimeEntry->started_at],
            ['ended_at', '<=', $this->worktimeEntry->ended_at]
        ])
        ->orWhere([
            ['started_at', '<=', $this->worktimeEntry->started_at],
            ['ended_at', '>=', $this->worktimeEntry->ended_at]
        ]);
    }
}
