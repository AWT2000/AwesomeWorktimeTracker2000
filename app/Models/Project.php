<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
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
     * Users that project belongs to as a personal project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_project', 'project_id', 'user_id');
    }

    /**
     * Teams that project belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_project', 'project_id', 'team_id');
    }

    /**
     * Founder of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function founder()
    {
        return $this->belongsTo(User::class, 'founder_id');
    }

    /**
     * Project manager of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }
}
