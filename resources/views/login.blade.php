@extends('template')

@section('content')
    <h1>Вход</h1>
    <form action="{{ url('/login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Почта</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ is_string(old('email')) ? old('email') : '' }}"
                autocomplete="username" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
@endsection
