@extends('layouts.app')
@section('title','Announcements')
@section('breadcrumb')<li class="breadcrumb-item active">Announcements</li>@endsection

@section('content')
<div class="row g-4">
    {{-- Post Form --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 sticky-top" style="top:80px;">
            <h6 class="fw-semibold mb-3"><i class="bi bi-megaphone me-2 text-primary"></i>Post Announcement</h6>
            <form method="POST" action="{{ route('instructor.announcements.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium" style="font-size:.82rem;">Title *</label>
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Announcement title..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="font-size:.82rem;">Message *</label>
                    <textarea name="body" class="form-control form-control-sm" rows="5" placeholder="Write your message..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="font-size:.82rem;">Target Subject</label>
                    <select name="subject_id" class="form-select form-select-sm">
                        <option value="">All Students</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="form-label fw-medium" style="font-size:.82rem;">Priority</label>
                        <select name="priority" class="form-select form-select-sm">
                            <option value="normal">🔵 Normal</option>
                            <option value="important">🟡 Important</option>
                            <option value="urgent">🔴 Urgent</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-medium" style="font-size:.82rem;">Pin?</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="is_pinned" value="1" class="form-check-input" id="pin">
                            <label class="form-check-label" for="pin" style="font-size:.82rem;">Pin it</label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="font-size:.82rem;">Expires At <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="datetime-local" name="expires_at" class="form-control form-control-sm">
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-sm">
                    <i class="bi bi-send me-1"></i>Post Announcement
                </button>
            </form>
        </div>
    </div>

    {{-- Announcements List --}}
    <div class="col-lg-8">
        <h6 class="fw-semibold mb-3">All Announcements <span class="badge bg-secondary-subtle text-secondary">{{ $announcements->total() }}</span></h6>
        @forelse($announcements as $ann)
        <div class="ct-card p-4 mb-3 {{ $ann->is_expired ? 'opacity-60' : '' }}" style="border-left:4px solid {{ $ann->priority_color }};">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        @if($ann->is_pinned) <i class="bi bi-pin-angle-fill text-warning" title="Pinned"></i> @endif
                        <span class="fw-semibold">{{ $ann->title }}</span>
                        <span class="badge" style="background:{{ $ann->priority_color }}22;color:{{ $ann->priority_color }};font-size:.65rem;">{{ ucfirst($ann->priority) }}</span>
                        @if($ann->subject)
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;"><i class="bi bi-book me-1"></i>{{ $ann->subject->code }}</span>
                        @else
                            <span class="badge bg-info-subtle text-info" style="font-size:.65rem;">All Students</span>
                        @endif
                        @if($ann->is_expired)
                            <span class="badge bg-secondary-subtle text-muted" style="font-size:.65rem;">Expired</span>
                        @endif
                    </div>
                    <p class="text-muted mb-2" style="font-size:.82rem;line-height:1.6;">{{ $ann->body }}</p>
                    <div class="d-flex gap-3 text-muted" style="font-size:.72rem;">
                        <span><i class="bi bi-person me-1"></i>{{ $ann->instructor->name }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $ann->created_at->diffForHumans() }}</span>
                        @if($ann->expires_at)
                            <span><i class="bi bi-calendar-x me-1"></i>Expires {{ $ann->expires_at->format('M j, Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex flex-column gap-1">
                    <form method="POST" action="{{ route('instructor.announcements.pin', $ann) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-light" title="{{ $ann->is_pinned ? 'Unpin' : 'Pin' }}">
                            <i class="bi bi-pin{{ $ann->is_pinned ? '-angle-fill text-warning' : '' }}"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('instructor.announcements.destroy', $ann) }}" onsubmit="return confirm('Delete this announcement?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="ct-card p-5 text-center">
            <i class="bi bi-megaphone text-muted" style="font-size:2.5rem;"></i>
            <p class="text-muted mt-3">No announcements yet. Post your first one!</p>
        </div>
        @endforelse
        {{ $announcements->links() }}
    </div>
</div>
@endsection
