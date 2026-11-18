@extends('template')

@section('content')
    <h1>Инвентарь</h1>
    <p>Игрок: {{ $player->name }}</p>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Предмет</th>
                <th scope="col">Количество</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <th scope="row">{{ $item->id }}</th>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Инвентарь пока пуст</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
