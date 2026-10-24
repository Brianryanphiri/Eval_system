<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Identity | Pro Tech Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --pts-black: #0F0F0F;
            --pts-green: #7AC142;
            --pts-green-dark: #5E9A31;
            --pts-grey: #6B7280;
            --pts-grey-light: #E5E7EB;
            --pts-white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            height: 100vh;
            overflow: hidden;
        }

        .auth-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: 100vh;
        }

        /* ---------- Left: image panel ---------- */
        .auth-visual {
            background: var(--pts-black) url('{{ asset('images/auth.jfif') }}') center/cover no-repeat;
            position: relative;
        }

        .auth-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 15, 15, 0.55) 0%, rgba(15, 15, 15, 0.85) 100%);
        }

        .auth-visual__content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            color: #fff;
        }

        .auth-visual__brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .auth-visual__brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: transparent;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pts-black);
            font-weight: 800;
            font-size: 18px;
        }

        .auth-visual__brand-name {
            font-weight: 700;
            font-size: 16px;
        }

        .auth-visual__brand-tag {
            font-size: 11px;
            color: #C9CBCF;
            letter-spacing: 0.05em;
        }

        .auth-visual__quote h2 {
            font-size: 30px;
            line-height: 1.3;
            font-weight: 700;
            max-width: 420px;
            margin-bottom: 14px;
        }

        .auth-visual__quote p {
            color: #C9CBCF;
            font-size: 14px;
            max-width: 380px;
        }

        /* ---------- Right: form panel ---------- */
        .auth-form {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pts-white);
            overflow-y: auto;
        }

        .auth-form__inner {
            width: 100%;
            max-width: 380px;
            padding: 40px 20px;
        }

        .auth-form__heading {
            font-size: 26px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .auth-form__sub {
            color: var(--pts-grey);
            font-size: 14px;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .auth-form__sub strong {
            color: #111827;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .code-input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.35em;
            text-align: center;
            color: #111827;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            font-family: inherit;
        }

        .code-input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background: var(--pts-black);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s;
            margin-top: 6px;
            font-family: inherit;
        }

        .btn-primary:hover {
            background: var(--pts-green-dark);
        }

        .auth-form__footer {
            text-align: center;
            margin-top: 26px;
            font-size: 13px;
            color: var(--pts-grey);
        }

        .auth-form__footer button {
            background: none;
            border: none;
            color: var(--pts-green-dark);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
            padding: 0;
        }

        .auth-form__footer button:hover {
            text-decoration: underline;
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

        .form-info {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        @media (max-width: 900px) {
            .auth-wrap {
                grid-template-columns: 1fr;
            }

            .auth-visual {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="auth-wrap">
        <div class="auth-visual">
            <div class="auth-visual__content">
                <div class="auth-visual__brand">
                    <div class="auth-visual__brand-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="Pro Tech Solutions"
                            style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <div class="auth-visual__brand-name">Pro Tech Solutions</div>
                        <div class="auth-visual__brand-tag">EVALUATION SYSTEM</div>
                    </div>
                </div>
                <div class="auth-visual__quote">
                    <h2>One more step to keep your school's data safe.</h2>
                    <p>Two-factor verification helps make sure it's really you signing in | a quick code, sent straight
                        to your inbox.</p>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <div class="auth-form__inner">
                <div class="auth-form__heading">Verify your identity</div>
                <div class="auth-form__sub">
                    We sent a 6-digit code to <strong>{{ $email }}</strong>. It expires in 10 minutes.
                </div>

                @if (session('info'))
                    <div class="form-info">{{ session('info') }}</div>
                @endif

                @if ($errors->any())
                    <div class="form-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('two-factor.verify.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Verification Code</label>
                        <input type="text" name="code" class="code-input" inputmode="numeric"
                            autocomplete="one-time-code" maxlength="6" placeholder="000000" autofocus>
                    </div>

                    <button type="submit" class="btn-primary">
                        Verify &amp; Continue <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-form__footer">
                    <form method="POST" action="{{ route('two-factor.resend') }}">
                        @csrf
                        <button type="submit">Didn't get a code? Resend</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>