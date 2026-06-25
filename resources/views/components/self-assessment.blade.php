@props(['log'])

@php $assessment = $log->selfAssessment; @endphp

<div class="ct-card p-4 mb-4">
    <h6 class="fw-semibold mb-3">
        <i class="bi bi-clipboard-check me-2 text-primary"></i>Self-Assessment
        @if($assessment)
            <span class="badge bg-success-subtle text-success ms-2" style="font-size:.65rem;">Completed</span>
        @endif
    </h6>

    @if($assessment)
    <div class="row g-3 mb-3">
        @foreach(['understanding' => 'Understanding', 'confidence' => 'Confidence', 'effort' => 'Effort'] as $key => $label)
        <div class="col-4 text-center">
            <div class="fw-bold fs-4" style="color:#4F46E5;">{{ $assessment->$key }}/5</div>
            <div class="text-muted" style="font-size:.75rem;">{{ $label }}</div>
            <div class="d-flex justify-content-center gap-1 mt-1">
                @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ $i <= $assessment->$key ? '-fill' : '' }}" style="color:#F59E0B;font-size:.7rem;"></i>
                @endfor
            </div>
        </div>
        @endforeach
    </div>
    @if($assessment->reflection)
        <div class="mb-2"><small class="text-muted fw-medium">Reflection</small><p class="mb-0" style="font-size:.85rem;">{{ $assessment->reflection }}</p></div>
    @endif
    @if($assessment->next_steps)
        <div><small class="text-muted fw-medium">Next Steps</small><p class="mb-0" style="font-size:.85rem;">{{ $assessment->next_steps }}</p></div>
    @endif
    <div class="mt-3 text-end">
        <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('assessForm').classList.toggle('d-none')">Update Assessment</button>
    </div>
    @endif

    <div id="assessForm" class="{{ $assessment ? 'd-none' : '' }}">
        <form method="POST" action="{{ route('student.logs.assessment.store', $log) }}">
            @csrf
            <div class="row g-3 mb-3">
                @foreach(['understanding' => 'Understanding 🧠', 'confidence' => 'Confidence 💪', 'effort' => 'Effort ⚡'] as $key => $label)
                <div class="col-4">
                    <label class="form-label fw-medium" style="font-size:.78rem;">{{ $label }}</label>
                    <div class="star-rating" data-field="{{ $key }}">
                        @for($i=1;$i<=5;$i++)
                        <i class="bi bi-star{{ $assessment && $assessment->$key >= $i ? '-fill active' : '' }}" data-val="{{ $i }}"></i>
                        @endfor
                        <input type="hidden" name="{{ $key }}" value="{{ $assessment?->$key ?? 3 }}" id="{{ $key }}Input">
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium" style="font-size:.78rem;">Reflection <span class="text-muted fw-normal">(optional)</span></label>
                <textarea name="reflection" class="form-control form-control-sm" rows="2"
                          placeholder="What did you learn? What was challenging?">{{ $assessment?->reflection }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium" style="font-size:.78rem;">Next Steps <span class="text-muted fw-normal">(optional)</span></label>
                <textarea name="next_steps" class="form-control form-control-sm" rows="2"
                          placeholder="What will you do next to improve?">{{ $assessment?->next_steps }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm px-4">
                <i class="bi bi-check me-1"></i>Save Assessment
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.star-rating').forEach(widget => {
    const stars = widget.querySelectorAll('.bi');
    const field = widget.dataset.field;
    const input = document.getElementById(field + 'Input');
    stars.forEach((star, idx) => {
        star.addEventListener('click', () => {
            input.value = idx + 1;
            stars.forEach((s, i) => {
                s.className = i <= idx ? 'bi bi-star-fill active' : 'bi bi-star';
            });
        });
        star.addEventListener('mouseenter', () => {
            stars.forEach((s, i) => s.style.color = i <= idx ? '#F59E0B' : '#CBD5E1');
        });
    });
    widget.addEventListener('mouseleave', () => {
        stars.forEach(s => s.style.color = '');
    });
});
</script>
@endpush
