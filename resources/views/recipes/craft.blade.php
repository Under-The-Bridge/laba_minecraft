<form action="{{ url('/craft') }}" method="POST">
    @csrf
    <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
    <button type="submit" class="btn btn-primary btn-sm">Скрафтить</button>
</form>
