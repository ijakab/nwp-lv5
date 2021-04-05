<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        User::create([
            'name' => $request->fullname,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        return View::make('login');
    }

    public function login(Request $request)
    {
        $user = User::firstWhere([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        return View::make('register');
    }
}
