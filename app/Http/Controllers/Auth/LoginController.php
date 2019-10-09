<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        return $this->middleware('guest', ['except' => 'logout']);
    }

    public function username()
    {
        return 'Nombre';
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
        $credentials = $this->validate(request(), [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            Auth::loginUsingId(Auth::user()->userTablePrimaryKey);
            return redirect()->route('home');
        }

        return back()
            ->withErrors([$this->username() => trans('auth.failed')])
            ->withInput(request([$this->username()]));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
