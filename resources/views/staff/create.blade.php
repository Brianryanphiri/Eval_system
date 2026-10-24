@extends('layouts.app')

@section('title', 'Add Staff')
@section('page-title', 'Add Staff')

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
        .form-select {
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
        .form-select:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
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

        @media (max-width: 1000px) {
            .form-grid-outer {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <a href="{{ route('staff.index') }}" class="form-header__back"><i class="fa-solid fa-arrow-left"></i> Back to Staff</a>
    <div class="form-header__title">Add Staff</div>
    <div class="form-header__sub">Create a new staff account. Login details are emailed automatically.</div>

    @if ($errors->any())
        <div class="form-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('staff.store') }}">
        @csrf

        <div class="form-grid-outer">
            <div>
                <div class="panel">
                    <div class="panel__section-title"><i class="fa-solid fa-user"></i> Staff Details</div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="name" class="form-input" value="{{ old('name') }}"
                                placeholder="Mr. Phiri" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role <span class="req">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role</option>
                                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="head_of_department" {{ old('role') === 'head_of_department' ? 'selected' : '' }}>Head of Department</option>
                                <option value="academic_director" {{ old('role') === 'academic_director' ? 'selected' : '' }}>
                                    Academic Director</option>
                                <option value="examiner" {{ old('role') === 'examiner' ? 'selected' : '' }}>Examiner /
                                    Moderator</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Email Address <span class="req">*</span></label>
                            <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                                placeholder="teacher@school.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-input" value="{{ old('phone') }}"
                                placeholder="+265 ...">
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="form-footer">
                        <a href="{{ route('staff.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Send
                            Invite</button>
                    </div>
                </div>
            </div>

            <div>
                <div class="panel preview-card">
                    <div class="preview-avatar"><i class="fa-solid fa-envelope"></i></div>
                    <div class="preview-note">
                        <strong>What happens next</strong>
                        <p style="margin-top:8px;">A temporary password is generated and emailed to this address. They will
                            be required to set their own password the first time they sign in.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection