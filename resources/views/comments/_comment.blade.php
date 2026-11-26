<div class="border-bottom py-2 comment">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $comment->user->name }}</strong>
            <small class="text-muted">· {{ $comment->created_at->diffForHumans() }}</small>
        </div>
        @can('delete', $comment)
            <button type="button" class="btn btn-link btn-sm text-danger p-0 delete-comment"
                    data-url="{{ route('comments.destroy', $comment) }}">Delete</button>
        @endcan
    </div>
    <p class="mb-0">{{ $comment->body }}</p>
</div>
