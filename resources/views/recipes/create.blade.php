@extends('template')

@section('content')
    <h1>Создать рецепт</h1>
    <form action="{{ url('/recipes') }}" method="POST">
        @csrf
        @include('recipes.form')
        <button type="submit" class="btn btn-primary">Создать</button>
    </form>
@endsection
