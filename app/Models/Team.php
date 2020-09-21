<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

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
     * Users that user belongs to team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_team', 'team_id', 'user_id');
    }

    /**
     * Users that user belongs to team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'team_project', 'team_id', 'project_id');
    }

    /**
     * Projects supervisor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }
}
