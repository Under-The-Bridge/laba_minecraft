<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['bail', 'required', 'string', 'min:8', 'confirmed', 'regex:/\A[^\x00]{1,72}\z/'],
        ], [
            'name.required' => 'Введи имя.',
            'name.max' => 'Имя должно быть не длиннее 255 символов.',
            'email.required' => 'Введи почту.',
            'email.email' => 'Введи адрес почты.',
            'email.max' => 'Адрес почты должен быть не длиннее 255 символов.',
            'email.unique' => 'Эта почта уже занята.',
            'password.required' => 'Введи пароль.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.regex' => 'Пароль слишком длинный или содержит недопустимый символ.',
        ]);

        try {
            $user = User::create($data);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Эта почта уже занята.']);
        }

        Auth::login($user);
        $request->session()->regenerate();
        return redirect('/');
    }

    public function login_page()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($data)) {
            return back()->withErrors(['email' => 'Неверная почта или пароль'])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
