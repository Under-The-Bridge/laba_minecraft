<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Инвентарь с крафтом</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid container">
            <a class="navbar-brand" href="{{ url('/') }}">Крафт</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Открыть меню">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Рецепты</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/recipes/create') }}">Создать</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/inventory') }}">Инвентарь</a>
                    </li>
                </ul>
                @auth
                    <span class="navbar-text me-3">{{ auth()->user()->name }}</span>
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Выйти</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ url('/login') }}">Войти</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container mt-3">
        @if (session('success'))
            <p class="alert alert-success" role="status">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="alert alert-danger" role="alert">{{ session('error') }}</p>
        @endif
        @error('recipe_id')
            <p class="alert alert-danger" role="alert">{{ $message }}</p>
        @enderror
        @yield('content')
    </main>
    <script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
</body>

</html>
