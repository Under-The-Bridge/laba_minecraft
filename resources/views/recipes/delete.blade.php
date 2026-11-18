<form action="{{ url('/recipes/'.$recipe->id) }}" method="POST"
    onsubmit="return confirm('Удалить этот рецепт?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
</form>
