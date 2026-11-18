@extends('template')

@section('content')
    <h1>{{ $recipe->name }}</h1>
    <p>Результат: {{ $recipe->resultItem->name }} — {{ $recipe->result_quantity }} шт.</p>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Ресурс</th>
                <th scope="col">Количество</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recipe->ingredients as $ingredient)
                <tr>
                    <td>{{ $ingredient->name }}</td>
                    <td>{{ $ingredient->pivot->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Ингредиентов пока нет</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex gap-3 align-items-center">
        @include('recipes.craft')
        <a href="{{ url('/recipes/'.$recipe->id.'/edit') }}">Редактировать</a>
        @include('recipes.delete')
        <a href="{{ url('/') }}">Все рецепты</a>
    </div>
@endsection
