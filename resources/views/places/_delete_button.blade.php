{{-- delete button for a place, used on the detail page and My places --}}
<form method="POST" action="{{ route('places.destroy', $place) }}" class="d-inline"
      onsubmit="return confirm('Delete this place? This cannot be undone.')">
    @csrf
    {{-- HTML forms can only POST, this hidden field tells Laravel it is a DELETE --}}
    @method('DELETE')
    <button type="submit" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-trash"></i> Delete
    </button>
</form>
