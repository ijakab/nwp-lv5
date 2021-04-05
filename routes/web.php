<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
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
        return redirect('/projects');
    }

    return view('register');
});

Route::get('/login', function (\Illuminate\Http\Request $request) {
    $loggedId = $request->session()->get('user.id');
    if ($loggedId) {
        return redirect('/projects');
    }

    return view('login');
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);

Route::get('/users', [UserController::class, 'show']);
Route::get('/change-role/{id}/{role}', [UserController::class, 'changeRole']);


Route::get('/tasks', [TaskController::class, 'show']);
Route::get('/task-create', function (\Illuminate\Http\Request $request) {
    $loggedRole = $request->session()->get('user.role');
    if ($loggedRole != 'teacher') return redirect('/tasks');

    return view('task-create');
});

Route::get('/apply/{taskId}', [TaskController::class, 'apply']);
