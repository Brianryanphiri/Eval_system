<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Assessment Model | Pro Tech Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --pts-black: #0F0F0F;
            --pts-green: #7AC142;
            --pts-green-dark: #5E9A31;
            --pts-green-light: #E9F5DD;
            --pts-grey: #6B7280;
            --pts-grey-light: #E5E7EB;
            --pts-bg: #F5F6F8;
            --pts-white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--pts-bg);
            min-height: 100vh;
        }

        .wizard-topbar {
            height: 72px;
            background: var(--pts-black);
            display: flex;
            align-items: center;
            padding: 0 40px;
        }

        .wizard-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .wizard-brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--pts-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-black);
            font-weight: 800;
        }

        .wizard-brand-text {
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }

        .wizard-brand-text span {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: #9CA3AF;
            letter-spacing: 0.06em;
        }

        .wizard-progress {
            max-width: 900px;
            margin: 40px auto 0;
            padding: 0 20px;
            display: flex;
            align-items: center;
        }

        .wizard-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
            position: relative;
        }

        .wizard-step__circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pts-white);
            border: 2px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--pts-grey);
            z-index: 2;
        }

        .wizard-step.active .wizard-step__circle {
            background: var(--pts-green);
            border-color: var(--pts-green);
            color: var(--pts-black);
        }

        .wizard-step.done .wizard-step__circle {
            background: var(--pts-black);
            border-color: var(--pts-black);
            color: #fff;
        }

        .wizard-step__label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--pts-grey);
            text-align: center;
        }

        .wizard-step.active .wizard-step__label,
        .wizard-step.done .wizard-step__label {
            color: #111827;
        }

        .wizard-step::after {
            content: "";
            position: absolute;
            top: 17px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--pts-grey-light);
            z-index: 1;
        }

        .wizard-step:last-child::after {
            display: none;
        }

        .wizard-step.done::after {
            background: var(--pts-black);
        }

        .wizard-content {
            max-width: 760px;
            margin: 48px auto 0;
            background: var(--pts-white);
            border-radius: 16px;
            border: 1px solid var(--pts-grey-light);
            padding: 44px;
        }

        .wizard-content__eyebrow {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-green-dark);
            margin-bottom: 8px;
        }

        .wizard-content__title {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
        }

        .wizard-content__sub {
            font-size: 14px;
            color: var(--pts-grey);
            margin-bottom: 32px;
            max-width: 560px;
        }

        .assess-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .assess-card {
            position: relative;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .assess-card:hover {
            border-color: var(--pts-green);
        }

        .assess-card input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .assess-card:has(input:checked) {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .assess-card:has(input:checked) .assess-card__box {
            background: var(--pts-green);
            border-color: var(--pts-green);
        }

        .assess-card:has(input:checked) .assess-card__box i {
            opacity: 1;
        }

        .assess-card__inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .assess-card__box {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            border: 1.5px solid var(--pts-grey-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #fff;
        }

        .assess-card__box i {
            font-size: 11px;
            color: var(--pts-black);
            opacity: 0;
        }

        .assess-card__name {
            font-size: 13.5px;
            font-weight: 600;
            color: #111827;
        }

        .assess-card__desc {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 1px;
        }

        .selection-counter {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 22px;
            padding: 12px 16px;
            background: var(--pts-bg);
            border-radius: 9px;
            font-size: 13px;
            color: #374151;
        }

        .selection-counter strong {
            color: var(--pts-green-dark);
        }

        .wizard-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid var(--pts-grey-light);
        }

        .btn-ghost {
            padding: 12px 20px;
            background: transparent;
            border: none;
            color: var(--pts-grey);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            padding: 12px 26px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        .btn-primary:disabled {
            background: var(--pts-grey-light);
            color: var(--pts-grey);
            cursor: not-allowed;
        }

        @media (max-width: 700px) {
            .assess-grid {
                grid-template-columns: 1fr;
            }

            .wizard-content {
                padding: 28px 20px;
                margin: 28px 16px 0;
            }
        }
    </style>
</head>

<body>

    <div class="wizard-topbar">
        <div class="wizard-brand">
            <div class="wizard-brand-icon" style="background: transparent; padding: 0;">
                <img src="{{ asset('images/logo.png') }}" alt="Pro Tech Solutions"
                    style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="wizard-brand-text">Pro Tech Solutions<span>SCHOOL SETUP</span></div>
        </div>
    </div>

    <div class="wizard-progress">
        <div class="wizard-step done">
            <div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div>
            <div class="wizard-step__label">Institution</div>
        </div>
        <div class="wizard-step active">
            <div class="wizard-step__circle">2</div>
            <div class="wizard-step__label">Assessment Model</div>
        </div>
        <div class="wizard-step">
            <div class="wizard-step__circle">3</div>
            <div class="wizard-step__label">Weighting</div>
        </div>
        <div class="wizard-step">
            <div class="wizard-step__circle">4</div>
            <div class="wizard-step__label">Grading</div>
        </div>
        <div class="wizard-step">
            <div class="wizard-step__circle">5</div>
            <div class="wizard-step__label">Promotion</div>
        </div>
    </div>

    <form method="POST" action="{{ route('setup.step2.store') }}">
        @csrf
        <div class="wizard-content">
            <div class="wizard-content__eyebrow">Step 2 of 5</div>
            <div class="wizard-content__title">How does your school evaluate students?</div>
            <div class="wizard-content__sub">Select every assessment type your school actually uses. The next step will
                let you assign a weight to each one you pick.</div>

            <div class="assess-grid">
                @php
                    $types = [
                        'continuous_assessment' => ['Continuous Assessment', 'Ongoing in-class evaluation', 'fa-list-check'],
                        'class_tests' => ['Class Tests', 'Short periodic tests', 'fa-file-lines'],
                        'assignments' => ['Assignments', 'Take-home tasks', 'fa-pen'],
                        'projects' => ['Projects', 'Extended research/practical work', 'fa-diagram-project'],
                        'mid_term' => ['Mid-Term Examinations', 'Term checkpoint exam', 'fa-calendar-check'],
                        'end_term' => ['End-of-Term Examinations', 'Full term summative exam', 'fa-calendar-days'],
                        'practical' => ['Practical Assessments', 'Lab / hands-on evaluation', 'fa-flask'],
                        'oral' => ['Oral Assessments', 'Spoken evaluation', 'fa-comments'],
                        'coursework' => ['Coursework', 'Ongoing graded work', 'fa-folder-open'],
                        'final_exam' => ['Final Examinations', 'End-of-year summative exam', 'fa-award'],
                        'competency' => ['Competency-Based Assessment', 'Skill/standard mastery checks', 'fa-bullseye'],
                    ];
                @endphp

                @foreach ($types as $value => [$name, $desc, $icon])
                    <label class="assess-card">
                        <input type="checkbox" name="assessment_types[]" value="{{ $value }}">
                        <div class="assess-card__inner">
                            <div class="assess-card__box"><i class="fa-solid fa-check"></i></div>
                            <div>
                                <div class="assess-card__name"><i class="fa-solid {{ $icon }}"
                                        style="color:var(--pts-grey); margin-right:6px; width:14px;"></i>{{ $name }}</div>
                                <div class="assess-card__desc">{{ $desc }}</div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="selection-counter">
                <i class="fa-regular fa-circle-question" style="color:var(--pts-green-dark);"></i>
                You can select multiple types, most schools combine continuous assessment with one or more
                examinations.
            </div>
        </div>

        <div class="wizard-content" style="margin-top:20px; padding-top:0; border:none; background:transparent;">
            <div class="wizard-footer" style="border-top:none; margin-top:0; padding-top:0;">
                <button type="button" class="btn-ghost" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i>
                    Back</button>
                <button type="submit" class="btn-primary">Continue <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
    </form>

</body>

</html>