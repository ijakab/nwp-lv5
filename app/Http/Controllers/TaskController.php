<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TaskController extends Controller
{
    public function show(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        $loggedRole = $request->session()->get('user.role');
        $tasks = [];

        if ($loggedRole == 'teacher') {
            $tasks = Task::where('teacher_id', $loggedId)->with('student')->get();
        } else if ($loggedRole == 'student') {
            $tasks = Task::whereNotNull('student_id')->with('student')->get();
        }

        return View::make('tasks', [
            'tasks' => $tasks,
            'loggedUserRole' => $loggedRole
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
        $project = Task::find($request->projectId);
        if ($project->leader_id != $loggedId) return View::make('login');

        $project->users()->attach($request->userId);
        return redirect('projects');
    }

    public function create(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        $loggedRole = $request->session()->get('user.role');
        if ($loggedRole != 'teacher') return redirect('/tasks');

        Task::create([
            'title' => $request->title,
            'title_en' => $request->title_en,
            'study_type' => $request->study_type,
            'teacher_id' => $loggedId,
        ]);

        return redirect('/tasks');
    }

    public function editForm(Request $request, $projectId)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $project = Task::where('id', $projectId)->first();

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

        $project = Task::where('id', $projectId)->first();
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
