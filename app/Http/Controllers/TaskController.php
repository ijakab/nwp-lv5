<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\Task;

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
            $tasks = Task::whereNull('student_id')->with('student')->get();
        }

        return View::make('tasks', [
            'tasks' => $tasks,
            'loggedUserRole' => $loggedRole
        ]);
    }

    public function applicants(Request $request, $taskId)
    {
        $loggedRole = $request->session()->get('user.role');
        if ($loggedRole != 'teacher') {
            return view('/tasks');
        }

        $task = Task::where('id', $taskId)->with('applicants')->first();
        return View::make('applicants', [
            'applicants' => $task->applicants,
            'task' => $task
        ]);
    }

    public function approve(Request $request, $taskId, $studentId)
    {
        $loggedId = $request->session()->get('user.id');
        $task = Task::where('id', $taskId)->with('applicants')->first();

        if ($task->teacher_id != $loggedId) return redirect('/tasks');
        $task->student_id = $studentId;
        $task->save();

        return redirect('/tasks');
    }

    public function apply(Request $request, $taskId)
    {
        $loggedId = $request->session()->get('user.id');
        $loggedRole = $request->session()->get('user.role');
        if ($loggedRole != 'student') {
            return view('/tasks');
        }

        $task = Task::find($taskId);
        $task->applicants()->attach($loggedId);
        return redirect('/tasks');
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
}
