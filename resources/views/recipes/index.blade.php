@extends('template')

@section('content')
    <div>
        <h1>Все рецепты</h1>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Результат</th>
                    <th scope="col">Количество</th>
                    <th scope="col">Ресурсы</th>
                    <th scope="col">Просмотр</th>
                    <th scope="col">Редакт.</th>
                    <th scope="col">Удалить</th>
                    <th scope="col">Скрафтить</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recipes as $recipe)
                    <tr>
                        <th scope="row">{{ $recipe->id }}</th>
                        <td>{{ $recipe->name }}</td>
                        <td>{{ $recipe->resultItem->name }}</td>
                        <td>{{ $recipe->result_quantity }}</td>
                        <td>
                            @foreach ($recipe->ingredients as $ingredient)
                                <div>{{ $ingredient->name }} — {{ $ingredient->pivot->quantity }}</div>
                            @endforeach
                        </td>
                        <td><a href="{{ url('/recipes/'.$recipe->id) }}">Просмотр</a></td>
                        <td><a href="{{ url('/recipes/'.$recipe->id.'/edit') }}">Редакт.</a></td>
                        <td>@include('recipes.delete')</td>
                        <td>@include('recipes.craft')</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">Рецептов пока нет</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
