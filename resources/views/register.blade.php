@extends('template')

@section('content')
    <h1>Регистрация</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ is_string(old('name')) ? old('name') : '' }}"
                autocomplete="nickname" maxlength="255" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Почта</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ is_string(old('email')) ? old('email') : '' }}"
                autocomplete="username" maxlength="255" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password"
                minlength="8" maxlength="72" required>
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Повтори пароль</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                autocomplete="new-password" minlength="8" maxlength="72" required>
        </div>

        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>

    <p class="mt-3">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
@endsection
