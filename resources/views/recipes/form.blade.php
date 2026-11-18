@php
    $name = old('name', $recipe->name ?? '');
    $result = old('result_item_id', $recipe->result_item_id ?? '');
    $quantity = old('result_quantity', $recipe->result_quantity ?? 1);
    $ingredients = session()->hasOldInput()
        ? collect(old('ingredients'))->filter(fn ($ingredient) => is_scalar(data_get($ingredient, 'id')))->keyBy('id')
        : collect(isset($recipe) ? $recipe->ingredients : [])->keyBy('id');
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Название</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ is_scalar($name) ? $name : '' }}"
        maxlength="255" required>
    @error('name')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="result_item_id" class="form-label">Результат</label>
    <select class="form-select" id="result_item_id" name="result_item_id" required>
        <option value="">Выбери предмет</option>
        @foreach ($results as $item)
            <option value="{{ $item->id }}" @selected($result == $item->id)>{{ $item->name }}</option>
        @endforeach
    </select>
    @error('result_item_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="result_quantity" class="form-label">Количество результата</label>
    <input type="number" class="form-control" id="result_quantity" name="result_quantity"
        value="{{ is_scalar($quantity) ? $quantity : '' }}" min="1" max="4294967295" required>
    @error('result_quantity')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<h2>Ресурсы</h2>
@error('ingredients')
    <div class="text-danger">{{ $message }}</div>
@enderror
@error('ingredients.*.id')
    <div class="text-danger">{{ $message }}</div>
@enderror
@error('ingredients.*.quantity')
    <div class="text-danger">{{ $message }}</div>
@enderror

<table class="table">
    <thead>
        <tr>
            <th scope="col">Ресурс</th>
            <th scope="col">Количество</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($resources as $resource)
            @php
                $ingredient = $ingredients->get($resource->id);
                $amount = data_get($ingredient, 'quantity', data_get($ingredient, 'pivot.quantity', 1));
            @endphp
            <tr>
                <td>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="resource_{{ $resource->id }}"
                            name="ingredients[{{ $resource->id }}][id]" value="{{ $resource->id }}"
                            onchange="document.getElementById('quantity_{{ $resource->id }}').disabled = !this.checked"
                            @checked($ingredient)>
                        <label class="form-check-label" for="resource_{{ $resource->id }}">{{ $resource->name }}</label>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control" id="quantity_{{ $resource->id }}"
                        name="ingredients[{{ $resource->id }}][quantity]" value="{{ is_scalar($amount) ? $amount : '' }}"
                        min="1" max="4294967295" aria-label="Количество: {{ $resource->name }}"
                        @disabled(!$ingredient) required>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
