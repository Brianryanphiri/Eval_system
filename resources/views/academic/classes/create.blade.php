@extends('layouts.app')

@section('title', 'Add Class')
@section('page-title', 'Add Class')

@section('styles')
<style>
    .form-header__back { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--pts-grey); text-decoration: none; margin-bottom: 14px; }
    .form-header__back:hover { color: var(--pts-green-dark); }
    .form-header__title { font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .form-header__sub { font-size: 13.5px; color: var(--pts-grey); margin-bottom: 24px; }

    .form-grid-outer { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }

    .panel { background: var(--pts-white); border: 1px solid var(--pts-grey-light); border-radius: 14px; padding: 28px; margin-bottom: 18px; }
    .panel__section-title {
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--pts-green-dark); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
    }

    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 18px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
    .form-label .req { color: #DC2626; }
    .form-input, .form-select {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
        font-size: 13.5px; color: #111827; outline: none; background: #fff; font-family: 'Inter', sans-serif;
    }
    .form-input:focus, .form-select:focus { border-color: var(--pts-green); box-shadow: 0 0 0 3px rgba(122,193,66,0.15); }
    .form-hint { font-size: 12px; color: var(--pts-grey); margin-top: 6px; }

    .form-error {
        background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA;
        padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px;
    }

    .form-footer { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
    .btn-secondary {
        padding: 12px 22px; background: #fff; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
        font-size: 13.5px; font-weight: 700; color: #374151; cursor: pointer; text-decoration: none;
    }
    .btn-primary {
        padding: 12px 24px; background: var(--pts-black); color: #fff; border: none; border-radius: 9px;
        font-size: 13.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;
    }
    .btn-primary:hover { background: var(--pts-green-dark); }

    .preview-card { text-align: center; }
    .preview-avatar {
        width: 72px; height: 72px; border-radius: 50%; background: var(--pts-bg); color: var(--pts-grey);
        display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 14px;
    }
    .preview-note { font-size: 12.5px; color: var(--pts-grey); line-height: 1.6; text-align: left; }
    .preview-note strong { color: #111827; }
    .preview-list { list-style: none; padding: 0; margin-top: 10px; }
    .preview-list li { display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: #374151; margin-bottom: 10px; }
    .preview-list li i { color: var(--pts-green-dark); margin-top: 2px; }

    @media (max-width: 1000px) { .form-grid-outer { grid-template-columns: 1fr; } }
    @media (max-width: 700px) { .form-grid-2 { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')

    <a href="{{ route('academic.index') }}" class="form-header__back"><i class="fa-solid fa-arrow-left"></i> Back to Classes and Subjects</a>
    <div class="form-header__title">Add Class</div>
    <div class="form-header__sub">Create a new class and optionally assign a homeroom teacher.</div>

    @if ($errors->any())
        <div class="form-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('classes.store') }}">
        @csrf

        <div class="form-grid-outer">
            <div>
                <div class="panel">
                    <div class="panel__section-title"><i class="fa-solid fa-chalkboard"></i> Class Details</div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Class Name <span class="req">*</span></label>
                            <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Form 2A" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Grade Level</label>
                            <input type="text" name="grade_level" class="form-input" value="{{ old('grade_level') }}" placeholder="Form 2">
                            <div class="form-hint">Groups streams together for reporting, e.g. Form 2A and Form 2B both under Form 2.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Homeroom Teacher</label>
                        <select name="homeroom_teacher_id" class="form-select">
                            <option value="">No homeroom teacher assigned</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="panel">
                    <div class="form-footer">
                        <a href="{{ route('academic.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Add Class</button>
                    </div>
                </div>
            </div>

            <div>
                <div class="panel preview-card">
                    <div class="preview-avatar"><i class="fa-solid fa-chalkboard"></i></div>
                    <div class="preview-note">
                        <strong>What happens next</strong>
                        <ul class="preview-list">
                            <li><i class="fa-solid fa-users"></i> Students can be enrolled into this class once it is created.</li>
                            <li><i class="fa-solid fa-book"></i> Subjects are attached to a class from the Subjects tab.</li>
                            <li><i class="fa-solid fa-user"></i> A homeroom teacher can be changed later from the class edit page.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection