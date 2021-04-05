<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectsController;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('register');
});

Route::get('/register', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if ($loggedId) {
        return view('welcome');
    }

    return view('register');
});

Route::get('/login', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if ($loggedId) {
        return view('welcome');
    }

    return view('login');
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);


Route::post('/project-create', [ProjectsController::class, 'create']);
Route::get('/projects', [ProjectsController::class, 'show']);
Route::get('/project-create', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if (!$loggedId) {
        return view('login');
    }

    return view('project-create');
});

Route::get('/project-assign/{projectId}', [ProjectsController::class, 'assignees']);
Route::post('/project-assign', [ProjectsController::class, 'assign']);

Route::get('/project-edit/{projectId}', [ProjectsController::class, 'editForm']);
Route::post('/project-edit/{projectId}', [ProjectsController::class, 'edit']);
