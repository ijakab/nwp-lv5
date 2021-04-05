<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $loggedRole = $request->session()->get('user.role');
        if ($loggedRole != 'admin') return redirect('/tasks');

        $users = User::all();
        return View::make('users', [
            'users' => $users
        ]);
    }

    public function changeRole(Request $request, $id, $role)
    {
        $loggedRole = $request->session()->get('user.role');
        if ($loggedRole != 'admin') redirect('/tasks');

        $user = User::where('id', $id)->first();
        $user->role = $role;
        $user->save();
        return redirect('/users');
    }

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
        if (!$user) return View::make('login');
        $request->session()->put('user.id', $user->id);
        $request->session()->put('user.email', $user->email);
        $request->session()->put('user.name', $user->name);
        $request->session()->put('user.role', $user->role);

        if ($user->role == 'admin') return redirect('/users');
        return redirect('/tasks');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user.id');
        $request->session()->forget('user.email');
        $request->session()->forget('user.name');
        $request->session()->forget('user.role');
        return redirect('/login');
    }
}
