@extends('layouts.app')

@section('title', 'Add Student')
@section('page-title', 'Add Student')

@section('styles')
    <style>
        .form-header__back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 14px;
        }

        .form-header__back:hover {
            color: var(--pts-green-dark);
        }

        .form-header__title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .form-header__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-bottom: 24px;
        }

        .form-grid-outer {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .panel {
            background: var(--pts-white);
            border: 1px solid var(--pts-grey-light);
            border-radius: 14px;
            padding: 28px;
            margin-bottom: 18px;
        }

        .panel__section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-green-dark);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-label .req {
            color: #DC2626;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            color: #111827;
            outline: none;
            background: #fff;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-error {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-secondary {
            padding: 12px 22px;
            background: #fff;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            color: #374151;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            padding: 12px 24px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        /* Sidebar preview card */
        .preview-card {
            text-align: center;
        }

        .preview-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--pts-bg);
            color: var(--pts-grey);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 14px;
        }

        .preview-note {
            font-size: 12.5px;
            color: var(--pts-grey);
            line-height: 1.6;
            text-align: left;
        }

        .preview-note strong {
            color: #111827;
        }

        .preview-list {
            list-style: none;
            padding: 0;
            margin-top: 10px;
        }

        .preview-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 12.5px;
            color: #374151;
            margin-bottom: 10px;
        }

        .preview-list li i {
            color: var(--pts-green-dark);
            margin-top: 2px;
        }

        @media (max-width: 1000px) {
            .form-grid-outer {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {

            .form-grid-2,
            .form-grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <a href="{{ route('students.index') }}" class="form-header__back"><i class="fa-solid fa-arrow-left"></i> Back to
        Students</a>
    <div class="form-header__title">Add Student</div>
    <div class="form-header__sub">Create a new student record and enroll them in a class.</div>

    @if ($errors->any())
        <div class="form-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('students.store') }}">
        @csrf

        <div class="form-grid-outer">
            <div>
                <div class="panel">
                    <div class="panel__section-title"><i class="fa-solid fa-user"></i> Student Details</div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}"
                                placeholder="John" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}"
                                placeholder="Banda" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="" selected>Select gender</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel__section-title"><i class="fa-solid fa-chalkboard"></i> Enrollment</div>

                    <div class="form-group">
                        <label class="form-label">Class <span class="req">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Select class</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('class_id') == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($classrooms->isEmpty())
                            <div style="font-size:12px; color:#DC2626; margin-top:6px;">
                                No classes exist yet. <a href="{{ route('academic.index') }}"
                                    style="color:#DC2626; text-decoration:underline;">Create one first</a>.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="panel">
                    <div class="form-footer">
                        <a href="{{ route('students.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Add Student</button>
                    </div>
                </div>
            </div>

            <div>
                <div class="panel preview-card">
                    <div class="preview-avatar"><i class="fa-solid fa-user"></i></div>
                    <div class="preview-note">
                        <strong>What happens next</strong>
                        <ul class="preview-list">
                            <li><i class="fa-solid fa-hashtag"></i> A student number is generated automatically once saved.
                            </li>
                            <li><i class="fa-solid fa-chalkboard"></i> The student is enrolled into the class you selected
                                for the current academic year.</li>
                            <li><i class="fa-solid fa-image"></i> A profile photo can be added afterward from the student's
                                profile page.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection