<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Grading Scale | Pro Tech Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --pts-black: #0F0F0F;
            --pts-green: #7AC142;
            --pts-green-dark: #5E9A31;
            --pts-green-light: #E9F5DD;
            --pts-red: #DC2626;
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
            margin-bottom: 26px;
            max-width: 580px;
        }

        .scale-presets {
            display: flex;
            gap: 10px;
            margin-bottom: 26px;
            flex-wrap: wrap;
        }

        .preset-btn {
            padding: 8px 14px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 20px;
            background: #fff;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
        }

        .preset-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
        }

        .grade-table-head,
        .grade-row {
            display: grid;
            grid-template-columns: 90px 1fr 90px 1fr 36px;
            gap: 10px;
            align-items: center;
        }

        .grade-table-head {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pts-grey);
            padding: 0 4px 8px;
            border-bottom: 1.5px solid var(--pts-grey-light);
            margin-bottom: 10px;
        }

        .grade-row {
            margin-bottom: 8px;
        }

        .grade-input {
            width: 100%;
            padding: 9px 10px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 13.5px;
            color: #111827;
            outline: none;
        }

        .grade-input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .grade-range {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .grade-range span {
            color: var(--pts-grey);
            font-size: 12px;
        }

        .remove-row-btn {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            border: 1.5px solid var(--pts-grey-light);
            background: #fff;
            color: var(--pts-red);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-row-btn:hover {
            background: #FEF2F2;
            border-color: #FECACA;
        }

        .add-row-btn {
            margin-top: 6px;
            padding: 10px 16px;
            border: 1.5px dashed var(--pts-grey-light);
            border-radius: 8px;
            background: transparent;
            color: var(--pts-grey);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
        }

        .add-row-btn:hover {
            border-color: var(--pts-green);
            color: var(--pts-green-dark);
            background: var(--pts-green-light);
        }

        .grade-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            padding: 12px 16px;
            background: var(--pts-bg);
            border-radius: 9px;
            font-size: 12.5px;
            color: #374151;
        }

        .wizard-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 30px;
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

        @media (max-width: 700px) {
            .wizard-content {
                padding: 24px 16px;
                margin: 28px 16px 0;
            }

            .grade-table-head,
            .grade-row {
                grid-template-columns: 70px 1fr 70px 1fr 32px;
                gap: 6px;
            }
        }
    </style>
</head>

<body>

    <div class="wizard-topbar">
        <div class="wizard-brand">
            <div class="wizard-brand-icon" style="background: transparent; padding: 0;">
                <img src="{{ asset('images/logo.png') }}" alt="Pro Tech Solutions" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="wizard-brand-text">Pro Tech Solutions<span>SCHOOL SETUP</span></div>
        </div>
    </div>

    <div class="wizard-progress">
        <div class="wizard-step done">
            <div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div>
            <div class="wizard-step__label">Institution</div>
        </div>
        <div class="wizard-step done">
            <div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div>
            <div class="wizard-step__label">Assessment Model</div>
        </div>
        <div class="wizard-step done">
            <div class="wizard-step__circle"><i class="fa-solid fa-check"></i></div>
            <div class="wizard-step__label">Weighting</div>
        </div>
        <div class="wizard-step active">
            <div class="wizard-step__circle">4</div>
            <div class="wizard-step__label">Grading</div>
        </div>
        <div class="wizard-step">
            <div class="wizard-step__circle">5</div>
            <div class="wizard-step__label">Promotion</div>
        </div>
    </div>

    <form method="POST" action="{{ route('setup.step4.store') }}" id="gradeForm">
        @csrf
        <div class="wizard-content">
            <div class="wizard-content__eyebrow">Step 4 of 5</div>
            <div class="wizard-content__title">Define your grading scale</div>
            <div class="wizard-content__sub">Set the score range, grade letter and meaning for each band. Start from a common preset or build your own from scratch.</div>

            <div class="scale-presets">
                <button type="button" class="preset-btn" data-preset="standard">Standard A–F</button>
                <button type="button" class="preset-btn" data-preset="plusminus">A+ / A / A- Scale</button>
                <button type="button" class="preset-btn" data-preset="blank">Start Blank</button>
            </div>

            <div class="grade-table-head">
                <div>Grade</div>
                <div>Score Range</div>
                <div></div>
                <div>Interpretation</div>
                <div></div>
            </div>

            <div id="gradeRows">
                {{-- rows injected here by JS / pre-filled from a preset --}}
            </div>

            <button type="button" class="add-row-btn" id="addRowBtn"><i class="fa-solid fa-plus"></i> Add grade band</button>

            <div class="grade-hint">
                <i class="fa-solid fa-circle-info" style="color:var(--pts-green-dark);"></i>
                Ranges should cover 0–100 with no gaps or overlaps. We'll validate this before you continue.
            </div>
        </div>

        <div class="wizard-content" style="margin-top:20px; padding-top:0; border:none; background:transparent;">
            <div class="wizard-footer" style="border-top:none; margin-top:0; padding-top:0;">
                <button type="button" class="btn-ghost" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-arrow-right"></i> Continue</button>
            </div>
        </div>
    </form>

    <script>
        const rowsContainer = document.getElementById('gradeRows');
        let rowCount = 0;

        function addRow(grade = '', min = '', max = '', interp = '') {
            rowCount++;
            const row = document.createElement('div');
            row.className = 'grade-row';
            row.innerHTML = `
                <input type="text" class="grade-input" name="grades[${rowCount}][letter]" placeholder="A" value="${grade}" required>
                <div class="grade-range">
                    <input type="number" class="grade-input" name="grades[${rowCount}][min]" placeholder="80" value="${min}" min="0" max="100" required>
                    <span>–</span>
                    <input type="number" class="grade-input" name="grades[${rowCount}][max]" placeholder="100" value="${max}" min="0" max="100" required>
                </div>
                <div></div>
                <input type="text" class="grade-input" name="grades[${rowCount}][interpretation]" placeholder="Excellent" value="${interp}" required>
                <button type="button" class="remove-row-btn" onclick="this.closest('.grade-row').remove()"><i class="fa-solid fa-trash"></i></button>
            `;
            rowsContainer.appendChild(row);
        }

        const presets = {
            standard: [
                ['A', 80, 100, 'Excellent'],
                ['B', 70, 79, 'Very Good'],
                ['C', 60, 69, 'Good'],
                ['D', 50, 59, 'Satisfactory'],
                ['E', 40, 49, 'Needs Improvement'],
                ['F', 0, 39, 'Unsatisfactory'],
            ],
            plusminus: [
                ['A+', 90, 100, 'Outstanding'],
                ['A', 80, 89, 'Excellent'],
                ['B', 70, 79, 'Very Good'],
                ['C', 60, 69, 'Good'],
                ['D', 50, 59, 'Satisfactory'],
                ['F', 0, 49, 'Unsatisfactory'],
            ],
        };

        function loadPreset(name) {
            rowsContainer.innerHTML = '';
            rowCount = 0;
            if (name === 'blank') { addRow(); return; }
            presets[name].forEach(([g, mn, mx, i]) => addRow(g, mn, mx, i));
        }

        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', () => loadPreset(btn.dataset.preset));
        });

        document.getElementById('addRowBtn').addEventListener('click', () => addRow());

        loadPreset('standard');
    </script>

</body>
</html>