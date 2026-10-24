<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Institution Type | Pro Tech Solutions</title>
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

        /* ---------- Top bar ---------- */
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

        /* ---------- Progress tracker ---------- */
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

        .wizard-step.active .wizard-step__label {
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

        /* ---------- Content card ---------- */
        .wizard-content {
            max-width: 760px;
            margin: 48px auto;
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
            max-width: 520px;
        }

        /* Institution type cards */
        .type-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 36px;
        }

        .type-card {
            position: relative;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 18px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .type-card:hover {
            border-color: var(--pts-green);
        }

        .type-card input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .type-card input:checked~.type-card__inner .type-card__icon {
            background: var(--pts-green);
            color: var(--pts-black);
        }

        .type-card input:checked~.type-card__inner .type-card__check {
            opacity: 1;
        }

        .type-card:has(input:checked) {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .type-card__inner {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .type-card__icon {
            width: 40px;
            height: 40px;
            border-radius: 9px;
            background: var(--pts-bg);
            color: var(--pts-grey);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .type-card__name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .type-card__desc {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .type-card__check {
            margin-left: auto;
            color: var(--pts-green-dark);
            opacity: 0;
            font-size: 16px;
        }

        /* Academic year row */
        .form-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-green-dark);
            margin-bottom: 14px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 14px;
            color: #111827;
            outline: none;
            background: #fff;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .form-hint {
            font-size: 12px;
            color: var(--pts-grey);
            margin-top: 6px;
        }

        /* Footer nav */
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

        @media (max-width: 700px) {

            .type-grid,
            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .wizard-content {
                padding: 28px 20px;
                margin: 28px 16px;
            }
        }
    </style>
</head>

<body>

    <div class="wizard-topbar">
        <div class="wizard-brand">
            <div class="wizard-brand-icon">P</div>
            <div class="wizard-brand-text">
                Pro Tech Solutions
                <span>SCHOOL SETUP</span>
            </div>
        </div>
    </div>

    <div class="wizard-progress">
        <div class="wizard-step active">
            <div class="wizard-step__circle">1</div>
            <div class="wizard-step__label">Institution</div>
        </div>
        <div class="wizard-step">
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

    <form method="POST" action="{{ route('setup.step1.store') }}">
        @csrf
        <div class="wizard-content">
            <div class="wizard-content__eyebrow">Step 1 of 5</div>
            <div class="wizard-content__title">Let's configure how your school evaluates learning</div>
            <div class="wizard-content__sub">The system will adapt its examination, grading, promotion and reporting
                engine to your school's assessment policy.</div>

            <div class="form-section-label">Institution Type</div>
            <div class="type-grid">
                <label class="type-card">
                    <input type="radio" name="institution_type" value="primary" required>
                    <div class="type-card__inner">
                        <div class="type-card__icon"><i class="fa-solid fa-child-reaching"></i></div>
                        <div>
                            <div class="type-card__name">Primary School</div>
                            <div class="type-card__desc">Foundation & lower grades</div>
                        </div>
                        <i class="fa-solid fa-circle-check type-card__check"></i>
                    </div>
                </label>

                <label class="type-card">
                    <input type="radio" name="institution_type" value="secondary" required>
                    <div class="type-card__inner">
                        <div class="type-card__icon"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div>
                            <div class="type-card__name">Secondary School</div>
                            <div class="type-card__desc">Form 1 – Form 4 / IGCSE</div>
                        </div>
                        <i class="fa-solid fa-circle-check type-card__check"></i>
                    </div>
                </label>

                <label class="type-card">
                    <input type="radio" name="institution_type" value="international" required>
                    <div class="type-card__inner">
                        <div class="type-card__icon"><i class="fa-solid fa-earth-africa"></i></div>
                        <div>
                            <div class="type-card__name">International School</div>
                            <div class="type-card__desc">Cambridge, IB & similar</div>
                        </div>
                        <i class="fa-solid fa-circle-check type-card__check"></i>
                    </div>
                </label>

                <label class="type-card">
                    <input type="radio" name="institution_type" value="college" required>
                    <div class="type-card__inner">
                        <div class="type-card__icon"><i class="fa-solid fa-building-columns"></i></div>
                        <div>
                            <div class="type-card__name">College / Training</div>
                            <div class="type-card__desc">Post-secondary institution</div>
                        </div>
                        <i class="fa-solid fa-circle-check type-card__check"></i>
                    </div>
                </label>
            </div>

            <div class="form-section-label">Academic Year</div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Current Academic Year</label>
                    <input type="text" name="academic_year" class="form-input" placeholder="2026" value="2026" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Term Structure</label>
                    <select name="term_structure" class="form-select" required>
                        <option value="" disabled selected>Select structure</option>
                        <option value="3_term">3 Terms</option>
                        <option value="2_semester">2 Semesters</option>
                        <option value="4_quarter">4 Quarters</option>
                        <option value="custom">Custom</option>
                    </select>
                    <div class="form-hint">You'll name and date each term on the next step.</div>
                </div>
            </div>
        </div>

        <div class="wizard-content" style="margin-top:-16px; padding-top:0; border:none; background:transparent;">
            <div class="wizard-footer" style="border-top:none; margin-top:0; padding-top:0;">
                <button type="button" class="btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to
                    registration</button>
                <button type="submit" class="btn-primary">Continue <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
    </form>

</body>

</html>