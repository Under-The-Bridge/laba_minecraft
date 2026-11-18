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
                    <th scope="col">Редактировать</th>
                    <th scope="col">Удалить</th>
                    <th scope="col">Скрафтить</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recipe as $item)
                    <tr>
                        <th scope="row">{{ $item->id }}</th>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->resultItem->name }}</td>
                        <td>{{ $item->result_quantity }}</td>
                        <td>
                            @foreach ($item->ingredients as $ingredient)
                                {{ $ingredient->name }} — {{ $ingredient->pivot->quantity }} шт.@if (!$loop->last)<br>@endif
                            @endforeach
                        </td>
                        <td><button class="btn btn-primary" type="button">Просмотр</button></td>
                        <td><button class="btn btn-warning" type="button">Редактировать</button></td>
                        <td><button class="btn btn-danger" type="button">Удалить</button></td>
                        <td><button class="btn btn-success" type="button">Скрафтить</button></td>
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
