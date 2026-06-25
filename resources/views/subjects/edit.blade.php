@extends('layouts.app')

@section('title', 'Edit Subject')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}" class="text-decoration-none">Subjects</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.show', $subject) }}" class="text-decoration-none">{{ $subject->code }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0">Edit Subject</h4>
            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <div class="ct-card p-4">
            <form method="POST" action="{{ route('subjects.update', $subject) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $subject->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-mono @error('code') is-invalid @enderror"
                               value="{{ old('code', $subject->code) }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Color</label>
                        <div class="input-group">
                            <input type="color" name="color" class="form-control form-control-color"
                                   value="{{ old('color', $subject->color) }}" style="max-width:60px;" id="colorPicker">
                            <input type="text" class="form-control font-mono" id="colorText"
                                   value="{{ old('color', $subject->color) }}" readonly style="background:#f8fafc;">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $subject->description) }}</textarea>
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end pt-2">
                        <a href="{{ route('subjects.show', $subject) }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Update Subject
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const c = document.getElementById('colorPicker');
    const t = document.getElementById('colorText');
    c.addEventListener('input', () => t.value = c.value);
</script>
@endpush
