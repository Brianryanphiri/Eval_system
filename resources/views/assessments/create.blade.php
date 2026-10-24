@extends('layouts.app')

@section('title', 'Create Assessment')
@section('page-title', 'Create Assessment')

@section('styles')
    <style>
        .form-wrap {
            max-width: 880px;
        }

        .form-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-header__back {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--pts-grey);
            text-decoration: none;
            margin-bottom: 10px;
        }

        .form-header__back:hover {
            color: var(--pts-green-dark);
        }

        .form-header__title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .form-header__sub {
            font-size: 13.5px;
            color: var(--pts-grey);
            margin-top: 4px;
        }

        .status-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            background: var(--pts-bg);
            color: var(--pts-grey);
            text-transform: uppercase;
            letter-spacing: 0.04em;
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

        .panel__section-title:not(:first-child) {
            margin-top: 8px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
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

        .form-input-suffix {
            position: relative;
        }

        .form-input-suffix span {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12.5px;
            color: var(--pts-grey);
            font-weight: 600;
            pointer-events: none;
        }

        .form-hint {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 6px;
        }

        /* Assessment type pills */
        .type-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .type-pill {
            position: relative;
        }

        .type-pill input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .type-pill label {
            display: block;
            padding: 9px 16px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.15s;
        }

        .type-pill input:checked+label {
            background: var(--pts-black);
            border-color: var(--pts-black);
            color: #fff;
        }

        /* Contribution slider preview */
        .contribution-preview {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
            padding: 12px 16px;
            background: var(--pts-bg);
            border-radius: 9px;
            font-size: 12.5px;
            color: #374151;
        }

        .contribution-preview strong {
            color: var(--pts-green-dark);
            font-size: 15px;
        }

        /* Footer */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-left {
            display: flex;
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
        }

        .btn-secondary:hover {
            border-color: var(--pts-grey);
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

        @media (max-width: 700px) {

            .form-grid-2,
            .form-grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')

    <div class="form-wrap">
        <a href="{{ route('assessments.index') }}" class="form-header__back"><i class="fa-solid fa-arrow-left"></i> Back to
            Assessments</a>
        <div class="form-header">
            <div>
                <div class="form-header__title">Create Assessment</div>
                <div class="form-header__sub">Set up a new examination, test, or continuous assessment component.</div>
            </div>
            <div class="status-badge">Draft</div>
        </div>

        <form method="POST" action="{{ route('assessments.store') }}">
            @csrf

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-file-lines"></i> Basic Details</div>

                <div class="form-group">
                    <label class="form-label">Assessment Name <span class="req">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Mid-Term Examination" required>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Academic Year <span class="req">*</span></label>
                        <select name="academic_year" class="form-select" required>
                            <option value="2026" selected>2026</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Term <span class="req">*</span></label>
                        <select name="term" class="form-select" required>
                            <option value="" disabled selected>Select term</option>
                            <option value="term1">Term 1</option>
                            <option value="term2">Term 2</option>
                            <option value="term3">Term 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Assessment Type <span class="req">*</span></label>
                        <select name="assessment_type" class="form-select" required>
                            <option value="" disabled selected>Select type</option>
                            <option value="formative">Formative</option>
                            <option value="summative">Summative</option>
                            <option value="diagnostic">Diagnostic</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Grade / Class <span class="req">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="" disabled selected>Select class</option>
                            <option value="form1">Form 1</option>
                            <option value="form2">Form 2</option>
                            <option value="form3">Form 3</option>
                            <option value="form4">Form 4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject <span class="req">*</span></label>
                        <select name="subject_id" class="form-select" required>
                            <option value="" disabled selected>Select subject</option>
                            <option value="math">Mathematics</option>
                            <option value="eng">English</option>
                            <option value="sci">Science</option>
                            <option value="hist">History</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-sliders"></i> Marking & Weighting</div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Total Marks <span class="req">*</span></label>
                        <input type="number" name="total_marks" class="form-input" placeholder="100" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration (minutes) <span class="req">*</span></label>
                        <input type="number" name="duration_minutes" class="form-input" placeholder="120" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pass Mark</label>
                        <div class="form-input-suffix">
                            <input type="number" name="pass_mark" class="form-input" placeholder="40" min="0" max="100"
                                id="passMark">
                            <span>%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contribution to Subject Total <span class="req">*</span></label>
                    <div class="form-input-suffix">
                        <input type="number" name="contribution" class="form-input" placeholder="30" min="0" max="100"
                            id="contributionInput" required>
                        <span>%</span>
                    </div>
                    <div class="form-hint">How much this assessment counts toward the subject's final term score — based on
                        your weighting policy from setup.</div>
                    <div class="contribution-preview">
                        <span>This assessment will contribute</span>
                        <strong id="contributionPreview">0%</strong>
                        <span>to the final subject score</span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__section-title"><i class="fa-solid fa-tags"></i> Applicable Assessment Components</div>
                <div class="form-hint" style="margin-bottom:14px; margin-top:-4px;">Select which of your school's assessment
                    types this belongs to.</div>
                <div class="type-pills">
                    <div class="type-pill">
                        <input type="radio" name="component" id="c1" value="continuous_assessment">
                        <label for="c1">Continuous Assessment</label>
                    </div>
                    <div class="type-pill">
                        <input type="radio" name="component" id="c2" value="mid_term">
                        <label for="c2">Mid-Term</label>
                    </div>
                    <div class="type-pill">
                        <input type="radio" name="component" id="c3" value="end_term">
                        <label for="c3">End-of-Term</label>
                    </div>
                    <div class="type-pill">
                        <input type="radio" name="component" id="c4" value="final_exam">
                        <label for="c4">Final Examination</label>
                    </div>
                    <div class="type-pill">
                        <input type="radio" name="component" id="c5" value="practical">
                        <label for="c5">Practical</label>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="form-footer">
                    <div class="footer-left">
                        <button type="button" class="btn-secondary">Save as Draft</button>
                    </div>
                    <button type="submit" class="btn-primary">
                        Create Assessment <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script>
        const contributionInput = document.getElementById('contributionInput');
        const contributionPreview = document.getElementById('contributionPreview');
        contributionInput.addEventListener('input', () => {
            contributionPreview.textContent = (contributionInput.value || 0) + '%';
        });
    </script>
@endsection