@extends('template')

@section('content')
    <h1>Редактировать рецепт</h1>
    <form action="{{ url('/recipes/'.$recipe->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('recipes.form')
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
@endsection
