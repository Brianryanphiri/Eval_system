<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Weighting | Pro Tech Solutions</title>
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
            --pts-red-light: #FEF2F2;
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
            margin-bottom: 30px;
            max-width: 560px;
        }

        .weight-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 16px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .weight-row__icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--pts-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-grey);
            font-size: 14px;
            flex-shrink: 0;
        }

        .weight-row__name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            flex: 1;
        }

        .weight-row__input-wrap {
            position: relative;
            width: 110px;
        }

        .weight-row__input {
            width: 100%;
            padding: 9px 30px 9px 12px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            text-align: right;
            outline: none;
        }

        .weight-row__input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .weight-row__pct {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pts-grey);
            font-size: 13px;
            font-weight: 600;
            pointer-events: none;
        }

        .weight-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            padding: 16px 20px;
            border-radius: 10px;
            background: var(--pts-red-light);
            border: 1.5px solid #FECACA;
            transition: all 0.2s;
        }

        .weight-total.is-valid {
            background: var(--pts-green-light);
            border-color: var(--pts-green);
        }

        .weight-total__label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .weight-total__value {
            font-size: 22px;
            font-weight: 800;
            color: var(--pts-red);
        }

        .weight-total.is-valid .weight-total__value {
            color: var(--pts-green-dark);
        }

        .weight-total__icon {
            font-size: 15px;
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

        .btn-primary:hover:not(:disabled) {
            background: var(--pts-green-dark);
        }

        .btn-primary:disabled {
            background: var(--pts-grey-light);
            color: var(--pts-grey);
            cursor: not-allowed;
        }

        @media (max-width: 700px) {
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
        <div class="wizard-step active">
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

    <form method="POST" action="{{ route('setup.step3.store') }}" id="weightForm">
        @csrf
        <div class="wizard-content">
            <div class="wizard-content__eyebrow">Step 3 of 5</div>
            <div class="wizard-content__title">Assign a weight to each assessment type</div>
            <div class="wizard-content__sub">These percentages must add up to exactly 100%. This becomes the default weighting for every subject | you'll be able to override it per subject later.</div>

            @foreach ($selectedTypes as $value => $label)
                <div class="weight-row">
                    <div class="weight-row__icon"><i class="fa-solid fa-scale-balanced"></i></div>
                    <div class="weight-row__name">{{ $label }}</div>
                    <div class="weight-row__input-wrap">
                        <input type="number" class="weight-row__input weight-input" name="weights[{{ $value }}]" min="0" max="100" value="0" required>
                        <span class="weight-row__pct">%</span>
                    </div>
                </div>
            @endforeach

            <div class="weight-total" id="weightTotal">
                <div class="weight-total__label"><i class="fa-solid fa-circle-info weight-total__icon"></i> Total weighting</div>
                <div class="weight-total__value" id="weightTotalValue">0%</div>
            </div>
        </div>

        <div class="wizard-content" style="margin-top:20px; padding-top:0; border:none; background:transparent;">
            <div class="wizard-footer" style="border-top:none; margin-top:0; padding-top:0;">
                <button type="button" class="btn-ghost" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <button type="submit" class="btn-primary" id="continueBtn" disabled>Continue <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
    </form>

    <script>
        const inputs = document.querySelectorAll('.weight-input');
        const totalBox = document.getElementById('weightTotal');
        const totalValue = document.getElementById('weightTotalValue');
        const continueBtn = document.getElementById('continueBtn');

        function updateTotal() {
            let sum = 0;
            inputs.forEach(i => sum += Number(i.value) || 0);
            totalValue.textContent = sum + '%';

            if (sum === 100) {
                totalBox.classList.add('is-valid');
                continueBtn.disabled = false;
            } else {
                totalBox.classList.remove('is-valid');
                continueBtn.disabled = true;
            }
        }

        inputs.forEach(i => i.addEventListener('input', updateTotal));
        updateTotal();
    </script>

</body>
</html>