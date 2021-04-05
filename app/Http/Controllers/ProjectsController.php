<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\Projects;

class ProjectsController extends Controller
{
    public function show(Request $request)
    {
        $loggedId = $request->session()->get('user.id');
        if (!$loggedId) {
            return view('login');
        }

        $projects = Projects::where('leader_id', $loggedId)->get();
        return View::make('projects', [
            'projects' => $projects,
            'loggedUserId' => $loggedId
        ]);
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
}
