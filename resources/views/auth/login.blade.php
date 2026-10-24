<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Pro Tech Solutions</title>
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
            background: var(--pts-green);
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

        .form-input-wrap {
            position: relative;
        }

        .form-input-wrap>i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pts-grey);
            font-size: 14px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input.has-toggle {
            padding-right: 44px;
        }

        .form-input:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .form-input::-ms-reveal,
        .form-input::-ms-clear {
            display: none;
        }

        .password-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--pts-grey);
            font-size: 14px;
            padding: 6px 8px;
            line-height: 1;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            color: var(--pts-green-dark);
            background: rgba(122, 193, 66, 0.08);
            outline: none;
        }

        .form-row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .form-remember {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #374151;
        }

        .form-forgot {
            color: var(--pts-green-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .form-forgot:hover {
            text-decoration: underline;
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

        .auth-form__footer a {
            color: var(--pts-green-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-form__footer a:hover {
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
                    <div class="auth-visual__brand-icon" style="background: transparent; padding: 0;">
                        <img src="{{ asset('images/logo.png') }}" alt="Pro Tech Solutions"
                            style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <div class="auth-visual__brand-name">Pro Tech Solutions</div>
                        <div class="auth-visual__brand-tag">EVALUATION SYSTEM</div>
                    </div>
                </div>
                <div class="auth-visual__quote">
                    <h2>Turning marks into meaningful learning insight.</h2>
                    <p>One platform for assessment, evaluation, and academic intervention | built for schools that want
                        more than just averages.</p>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <div class="auth-form__inner">
                <div class="auth-form__heading">Welcome back</div>
                <div class="auth-form__sub">Sign in to your Pro Tech Solutions account</div>

                @if ($errors->any())
                    <div class="form-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="form-input-wrap">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" class="form-input" placeholder="you@school.com" required
                                autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="form-input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="passwordInput" class="form-input has-toggle"
                                placeholder="••••••••" required>
                            <button type="button" class="password-toggle" id="passwordToggle"
                                aria-label="Show password">
                                <i class="fa-regular fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row-between">
                        <label class="form-remember">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="form-forgot">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        Sign In <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-form__footer">
                    Don't have an account? <a href="{{ route('register') }}">Register your school</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('passwordInput');
            var toggle = document.getElementById('passwordToggle');
            var icon = document.getElementById('passwordToggleIcon');

            if (!input || !toggle || !icon) return;

            function apply(showing) {
                input.type = showing ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !showing);
                icon.classList.toggle('fa-eye-slash', showing);
                toggle.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
            }

            toggle.addEventListener('click', function () {
                apply(input.type === 'password');
            });

            toggle.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    apply(input.type === 'password');
                }
            });
        })();
    </script>

</body>

</html>