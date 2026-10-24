<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Promotion Rules | Pro Tech Solutions</title>
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
            max-width: 800px;
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
            margin-bottom: 28px;
            max-width: 580px;
        }

        .form-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-green-dark);
            margin: 26px 0 14px;
        }

        .form-section-label:first-of-type {
            margin-top: 0;
        }

        .rule-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .rule-card {
            position: relative;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .rule-card:hover {
            border-color: var(--pts-green);
        }

        .rule-card input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }

        .rule-card:has(input:checked) {
            border-color: var(--pts-green);
            background: var(--pts-green-light);
        }

        .rule-card__inner {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .rule-card__icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--pts-bg);
            color: var(--pts-grey);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .rule-card:has(input:checked) .rule-card__icon {
            background: var(--pts-green);
            color: var(--pts-black);
        }

        .rule-card__name {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
        }

        .rule-card__desc {
            font-size: 11.5px;
            color: var(--pts-grey);
            margin-top: 2px;
        }

        .condition-box {
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 12px;
            padding: 20px;
            margin-top: 14px;
            background: var(--pts-bg);
        }

        .condition-row {
            display: grid;
            grid-template-columns: 1fr 130px 90px 36px;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .condition-row:last-child {
            margin-bottom: 0;
        }

        .condition-input,
        .condition-select {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13px;
            color: #111827;
            outline: none;
            background: #fff;}
        .condition-input:focus, .condition-select:focus { border-color: var(--pts-green); box-shadow: 0 0 0 3px rgba(122,193,66,0.15); }
        .condition-remove {
            width: 32px; height: 32px; border-radius: 7px; border: 1.5px solid var(--pts-grey-light);
            background: #fff; color: #DC2626; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .add-condition-btn {
            margin-top: 4px; padding: 9px 14px; border: 1.5px dashed var(--pts-grey-light); border-radius: 8px;
            background: transparent; color: var(--pts-grey); font-size: 12.5px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 6px;
        }
        .add-condition-btn:hover { border-color: var(--pts-green); color: var(--pts-green-dark); }

        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 0; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 12px 14px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px; font-size: 14px; color: #111827; outline: none; }
        .form-input:focus { border-color: var(--pts-green); box-shadow: 0 0 0 3px rgba(122,193,66,0.15); }

        .rule-preview {
            margin-top: 22px; padding: 16px 18px; background: var(--pts-black); border-radius: 10px;
            font-family: 'Courier New', monospace; font-size: 12.5px; color: #86EFAC; line-height: 1.7;
        }
        .rule-preview__label { color: #9CA3AF; font-family: 'Inter', sans-serif; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }

        .wizard-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; padding-top: 24px; border-top: 1px solid var(--pts-grey-light); }
        .btn-ghost { padding: 12px 20px; background: transparent; border: none; color: var(--pts-grey); font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .btn-primary {
            padding: 13px 28px; background: var(--pts-green); color: var(--pts-black); border: none; border-radius: 9px;
            font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;
        }
        .btn-primary:hover { background: var(--pts-green-dark); color: #fff; }

        @media (max-width: 700px) {
            .rule-grid, .form-grid-2 { grid-template-columns: 1fr; }
            .condition-row { grid-template-columns: 1fr; }
            .wizard-content { padding: 24px 16px; margin: 28px 16px 0; }
        }
    </style>
</head>
<body>

    <div class="wizard-topbar">
        <div class="wizard-brand">
            <div class="wizard-brand-icon">P</div>
            <div class="wizard-brand-text">Pro Tech Solutions<span>SCHOOL SETUP</span></div>
        </div>
    </div>

    <div class="wizard-progress">
        <div class="wizard-step done"><div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div><div class="wizard-step__label">Institution</div></div>
        <div class="wizard-step done"><div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div><div class="wizard-step__label">Assessment Model</div></div>
        <div class="wizard-step done"><div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div><div class="wizard-step__label">Weighting</div></div>
        <div class="wizard-step done"><div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div><div class="wizard-step__label">Grading</div></div>
        <div class="wizard-step active"><div class="wizard-step__circle">5</div><div class="wizard-step__label">Promotion</div></div>
    </div>

    <form method="POST" action="{{ route('setup.step5.store') }}" id="promoForm">
        @csrf
        <div class="wizard-content">
            <div class="wizard-content__eyebrow">Step 5 of 5 — Final Step</div>
            <div class="wizard-content__title">How are students promoted?</div>
            <div class="wizard-content__sub">Choose the base rule, then optionally add per-subject minimums or a cap on failed subjects. This becomes the automatic logic the system uses to mark students Promoted, Conditionally Promoted, or Not Promoted.</div>

            <div class="form-section-label">Base Promotion Rule</div>
            <div class="rule-grid">
                <label class="rule-card">
                    <input type="radio" name="promotion_rule" value="overall_average" required>
                    <div class="rule-card__inner">
                        <div class="rule-card__icon"><i class="fa-solid fa-percent"></i></div>
                        <div>
                            <div class="rule-card__name">Overall Average</div>
                            <div class="rule-card__desc">Promote based on combined score</div>
                        </div>
                    </div>
                </label>
                <label class="rule-card">
                    <input type="radio" name="promotion_rule" value="all_compulsory" required>
                    <div class="rule-card__inner">
                        <div class="rule-card__icon"><i class="fa-solid fa-list-check"></i></div>
                        <div>
                            <div class="rule-card__name">Pass Every Compulsory Subject</div>
                            <div class="rule-card__desc">No compulsory subject can fail</div>
                        </div>
                    </div>
                </label>
                <label class="rule-card">
                    <input type="radio" name="promotion_rule" value="min_passed" required>
                    <div class="rule-card__inner">
                        <div class="rule-card__icon"><i class="fa-solid fa-hashtag"></i></div>
                        <div>
                            <div class="rule-card__name">Minimum Subjects Passed</div>
                            <div class="rule-card__desc">Requires a set number of passes</div>
                        </div>
                    </div>
                </label>
                <label class="rule-card">
                    <input type="radio" name="promotion_rule" value="credit_based" required>
                    <div class="rule-card__inner">
                        <div class="rule-card__icon"><i class="fa-solid fa-layer-group"></i></div>
                        <div>
                            <div class="rule-card__name">Credit-Based Progression</div>
                            <div class="rule-card__desc">Accumulated credits/units</div>
                        </div>
                    </div>
                </label>
                <label class="rule-card">
                    <input type="radio" name="promotion_rule" value="board_decision" required>
                    <div class="rule-card__inner">
                        <div class="rule-card__icon"><i class="fa-solid fa-gavel"></i></div>
                        <div>
                            <div class="rule-card__name">Academic Board Decision</div>
                            <div class="rule-card__desc">Manual review, no automation</div>
                        </div>
                    </div>
                </label>
                <label class="rule-card">
                    <input type="radio" name="promotion_rule" value="custom" required>
                    <div class="rule-card__inner">
                        <div class="rule-card__icon"><i class="fa-solid fa-sliders"></i></div>
                        <div>
                            <div class="rule-card__name">Custom Rule</div>
                            <div class="rule-card__desc">Fully define your own logic</div>
                        </div>
                    </div>
                </label>
            </div>

            <div class="form-section-label">Additional Conditions (Optional)</div>
            <div class="form-grid-2" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label">Minimum overall average to pass</label>
                    <input type="number" name="min_overall_average" class="form-input" placeholder="e.g. 50" min="0" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Maximum failed subjects allowed</label>
                    <input type="number" name="max_failed_subjects" class="form-input" placeholder="e.g. 2" min="0">
                </div>
            </div>

            <div class="condition-box">
                <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:12px;">Per-subject minimums</div>
                <div id="conditionRows">
                    {{-- rows added here --}}
                </div>
                <button type="button" class="add-condition-btn" id="addConditionBtn"><i class="fa-solid fa-plus"></i> Add subject requirement</button>
            </div>

            <div class="rule-preview">
                <div class="rule-preview__label">Live Rule Preview</div>
                <div id="rulePreviewText">PROMOTION RULE<br>—</div>
            </div>
        </div>

        <div class="wizard-content" style="margin-top:20px; padding-top:0; border:none; background:transparent;">
            <div class="wizard-footer" style="border-top:none; margin-top:0; padding-top:0;">
                <button type="button" class="btn-ghost" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-flag-checkered"></i> Finish Setup</button>
            </div>
        </div>
    </form>

    <script>
        const conditionRows = document.getElementById('conditionRows');
        let condCount = 0;

        function addConditionRow(subject = '', min = '') {
            condCount++;
            const row = document.createElement('div');
            row.className = 'condition-row';
            row.innerHTML = `
                <input type="text" class="condition-input" name="subject_conditions[${condCount}][subject]" placeholder="e.g. Mathematics" value="${subject}">
                <select class="condition-select" name="subject_conditions[${condCount}][operator]">
                    <option value=">=">≥ (at least)</option>
                    <option value=">">&gt; (more than)</option>
                </select>
                <input type="number" class="condition-input" name="subject_conditions[${condCount}][value]" placeholder="40" min="0" max="100" value="${min}">
                <button type="button" class="condition-remove" onclick="this.closest('.condition-row').remove(); updatePreview();"><i class="fa-solid fa-trash"></i></button>
            `;
            conditionRows.appendChild(row);
            row.querySelectorAll('input, select').forEach(el => el.addEventListener('input', updatePreview));
        }

        document.getElementById('addConditionBtn').addEventListener('click', () => addConditionRow());

        function updatePreview() {
            const ruleType = document.querySelector('input[name="promotion_rule"]:checked');
            const avg = document.querySelector('input[name="min_overall_average"]').value;
            const maxFail = document.querySelector('input[name="max_failed_subjects"]').value;
            let lines = [];

            if (ruleType) {
                const labels = {
                    overall_average: 'Overall average ≥ ' + (avg || '__') + '%',
                    all_compulsory: 'Pass every compulsory subject',
                    min_passed: 'Minimum subjects passed required',
                    credit_based: 'Sufficient credits accumulated',
                    board_decision: 'Subject to academic board review',
                    custom: 'Custom rule (defined below)'
                };
                lines.push(labels[ruleType.value]);
            }
            if (avg && ruleType && ruleType.value !== 'overall_average') lines.push('Overall average ≥ ' + avg + '%');
            if (maxFail) lines.push('Maximum failed subjects = ' + maxFail);

            document.querySelectorAll('.condition-row').forEach(row => {
                const subj = row.querySelector('[name*="[subject]"]').value;
                const op = row.querySelector('[name*="[operator]"]').value;
                const val = row.querySelector('[name*="[value]"]').value;
                if (subj && val) lines.push(subj + ' ' + op + ' ' + val + '%');
            });

            document.getElementById('rulePreviewText').innerHTML =
                'PROMOTION RULE<br>' + (lines.length ? lines.join('<br><br>AND<br><br>') : '—');
        }

        document.querySelectorAll('input[name="promotion_rule"], input[name="min_overall_average"], input[name="max_failed_subjects"]')
            .forEach(el => el.addEventListener('input', updatePreview));

        addConditionRow();
    </script>

</body>
</html>