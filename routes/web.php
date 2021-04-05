<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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
    return view('welcome');
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

