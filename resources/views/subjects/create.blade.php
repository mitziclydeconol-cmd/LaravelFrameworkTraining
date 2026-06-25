@extends('layouts.app')

@section('title', 'Create Subject')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}" class="text-decoration-none">Subjects</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">Create Subject</h4>
                <p class="text-muted mb-0" style="font-size:.875rem;">Add a new curriculum subject</p>
            </div>
            <a href="{{ route('subjects.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>

        <div class="ct-card p-4">
            <form method="POST" action="{{ route('subjects.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Object-Oriented Programming" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-mono @error('code') is-invalid @enderror"
                               placeholder="e.g. OOP101" value="{{ old('code') }}" required style="text-transform:uppercase;">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Color</label>
                        <div class="input-group">
                            <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                                   value="{{ old('color', '#4F46E5') }}" style="max-width:60px;">
                            <input type="text" class="form-control font-mono" id="colorText"
                                   value="{{ old('color', '#4F46E5') }}" readonly style="background:#f8fafc;">
                        </div>
                        @error('color')<div class="text-danger" style="font-size:.82rem;">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Brief description of the subject...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end pt-2">
                        <a href="{{ route('subjects.index') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Create Subject
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
    const colorInput = document.querySelector('input[type="color"]');
    const colorText  = document.getElementById('colorText');
    colorInput.addEventListener('input', () => colorText.value = colorInput.value);
</script>
@endpush
