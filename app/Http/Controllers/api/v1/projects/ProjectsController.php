<?php

namespace App\Http\Controllers\api\v1\projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\projects\ProjectResource;
use Illuminate\Http\Request;
use App\Http\Requests\api\v1\projects\GetProjectsRequest;
use App\Http\Resources\projects\ProjectCollection;

class ProjectsController extends Controller
{
    public function index(GetProjectsRequest $request)
    {
        $user = $request->user();

        // admins get all projects, others only projects that belongs to them
        if ($user->hasRole('admin')) {
            $projects = Project::all();
        } else {
            $user->load(['teams.projects', 'personalProjects']);

            $projects = $user->personalProjects;

            $user->teams->each(function($team) use(&$projects) {
                $projects = $projects->merge($team->projects);
            });
        }

        // load project founder and manager
        $projects->load([
            'founder' => function($query) {
                $query->select(['id', 'name', 'email']);
            },
            'projectManager' => function($query) {
                $query->select(['id', 'name', 'email']);
            }
        ]);

        // mark projects as personal, if project is not assigned via team
        if (! $user->hasRole('admin')) {
            $projects->each(function($project) use($user) {
                if ($user->personalProjects->contains('id', $project->id)) {
                    $project->is_personal = true;
                } else {
                    $project->is_personal = false;
                }
            });
        }

        return new ProjectCollection($projects);
    }

    public function show(Project $project)
    {
        # code...
    }
}
