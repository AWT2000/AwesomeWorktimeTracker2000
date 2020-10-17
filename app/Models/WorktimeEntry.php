<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorktimeEntry extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'worktime_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'project_id', 'started_at', 'ended_at'];

    protected $dates = ['started_at', 'ended_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'pivot'
    ];

    /**
     * The user that the entry belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The project that the entry belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Colliding entries.
     *
     * (this entry -* other entries)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collidingEntries()
    {
        return $this->belongsToMany(
            WorktimeEntry::class,
            'entry_collisions',
            'worktime_entry_one_id',
            'worktime_entry_two_id');
    }

    /**
     * Entries that entry collides with.
     *
     * (other entries *- this entry)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entriesCollidingWith()
    {
        return $this->belongsToMany(
            WorktimeEntry::class,
            'entry_collisions',
            'worktime_entry_two_id',
            'worktime_entry_one_id');
    }
}
