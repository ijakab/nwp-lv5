<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ProjectsController extends Controller
{
    public function show(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $projects = Projects::where('leader_id', $loggedId)->orWhereHas('users', function (Builder $query) use ($loggedId) {
            $query->where('users.id', '=', $loggedId);
        })->get();
        return View::make('projects', [
            'projects' => $projects,
            'loggedUserId' => $loggedId
        ]);
    }

    public function assignees(Request $request, $id)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $users = User::all();
        return View::make('project-assign', [
            'users' => $users,
            'projectId' => $id
        ]);
    }

    public function assign(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }
        $project = Projects::find($request->projectId);
        if ($project->leader_id != $loggedId) return View::make('login');

        $project->users()->attach($request->userId);
        return redirect('projects');
    }

    public function create(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        Projects::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'jobs_done' => $request->jobs_done,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'leader_id' => $loggedId,
        ]);

        return View::make('project-create');
    }

    public function editForm(Request $request, $projectId)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $project = Projects::where('id', $projectId)->first();

        return View::make('project-edit', [
            'project' => $project,
            'loggedUserId' => $loggedId
        ]);
    }

    public function edit(Request $request, $projectId)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $project = Projects::where('id', $projectId)->first();
        if ($project->leader_id == $loggedId) {
            $project->title = $request->title;
            $project->description = $request->description;
            $project->price = $request->price;
            $project->jobs_done = $request->jobs_done;
            $project->starts_at = $request->starts_at;
            $project->ends_at = $request->ends_at;
        } else {
            $project->jobs_done = $request->jobs_done;
        }
        $project->save();

        return redirect('/projects');
    }
}
