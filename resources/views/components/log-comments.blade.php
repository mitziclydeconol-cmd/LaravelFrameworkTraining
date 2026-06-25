@props(['log'])

<div class="ct-card p-4 mb-4">
    <h6 class="fw-semibold mb-3">
        <i class="bi bi-chat-dots me-2 text-primary"></i>
        Comments <span class="badge bg-secondary-subtle text-secondary">{{ $log->comments->count() }}</span>
    </h6>

    {{-- Comment List --}}
    @forelse($log->comments as $comment)
    <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
             style="width:34px;height:34px;background:linear-gradient(135deg,{{ $comment->user->isInstructor() ? '#7C3AED,#4F46E5' : '#4F46E5,#06B6D4' }});font-size:.72rem;">
            {{ $comment->user->initials }}
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-medium" style="font-size:.82rem;">{{ $comment->user->name }}</span>
                @if($comment->user->isInstructor())
                    <span class="badge" style="background:#7C3AED22;color:#7C3AED;font-size:.6rem;">Instructor</span>
                @endif
                <span class="text-muted" style="font-size:.72rem;">{{ $comment->created_at->diffForHumans() }}</span>
                @if(auth()->id() === $comment->user_id || auth()->user()->isInstructor())
                <form method="POST" action="{{ route('logs.comments.destroy', $comment) }}" class="ms-auto" onsubmit="return confirm('Delete comment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="font-size:.7rem;"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </div>
            <p class="mb-0 text-secondary" style="font-size:.85rem;line-height:1.6;">{{ $comment->body }}</p>
        </div>
    </div>
    @empty
    <p class="text-muted text-center py-2 mb-3" style="font-size:.82rem;">No comments yet. Be the first!</p>
    @endforelse

    {{-- Post Comment --}}
    @if(auth()->id() === $log->user_id || auth()->user()->isInstructor())
    <form method="POST" action="{{ route('logs.comments.store', $log) }}" class="d-flex gap-2 mt-2">
        @csrf
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
             style="width:34px;height:34px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.72rem;">
            {{ auth()->user()->initials }}
        </div>
        <div class="flex-grow-1">
            <textarea name="body" class="form-control form-control-sm mb-2" rows="2"
                      placeholder="{{ auth()->user()->isInstructor() ? 'Leave feedback for this student...' : 'Add a note...' }}" required></textarea>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-send me-1"></i>Post
            </button>
        </div>
    </form>
    @endif
</div>
