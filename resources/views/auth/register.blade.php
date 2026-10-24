<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your School | Pro Tech Solutions</title>
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
            min-height: 100vh;
        }

        .auth-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        .auth-visual {
            position: relative;
            background: var(--pts-black) url('{{ asset('images/register.png') }}') center/cover no-repeat;
        }

        .auth-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 15, 15, 0.55) 0%, rgba(15, 15, 15, 0.88) 100%);
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
            font-size: 28px;
            line-height: 1.3;
            font-weight: 700;
            max-width: 420px;
            margin-bottom: 14px;
        }

        .auth-visual__quote p {
            color: #C9CBCF;
            font-size: 14px;
            max-width: 380px;
            margin-bottom: 22px;
        }

        .auth-visual__checklist {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .auth-visual__checklist li {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #E5E7EB;
        }

        .auth-visual__checklist i {
            color: var(--pts-green);
        }

        .auth-form {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pts-white);
            padding: 40px 0;
        }

        .auth-form__inner {
            width: 100%;
            max-width: 440px;
            padding: 0 20px;
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
            margin-bottom: 28px;
        }

        .form-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pts-green-dark);
            margin: 22px 0 12px;
        }

        .form-section-label:first-of-type {
            margin-top: 0;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
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

        .form-input-wrap {
            position: relative;
        }

        .form-input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pts-grey);
            font-size: 14px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid var(--pts-grey-light);
            border-radius: 9px;
            font-size: 14px;
            color: #111827;
            outline: none;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--pts-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.15);
        }

        .form-terms {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 12.5px;
            color: #4B5563;
            margin: 20px 0;
        }

        .form-terms a {
            color: var(--pts-green-dark);
            font-weight: 600;
            text-decoration: none;
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
            margin-top: 22px;
            font-size: 13px;
            color: var(--pts-grey);
        }

        .auth-form__footer a {
            color: var(--pts-green-dark);
            font-weight: 600;
            text-decoration: none;
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
                    <div class="auth-visual__brand-icon">P</div>
                    <div>
                        <div class="auth-visual__brand-name">Pro Tech Solutions</div>
                        <div class="auth-visual__brand-tag">EVALUATION SYSTEM</div>
                    </div>
                </div>
                <div>
                    <div class="auth-visual__quote">
                        <h2>Set up your school in minutes.</h2>
                        <p>You'll configure your assessment policy, grading scale and promotion rules right after this |
                            no coding, no fixed template.</p>
                    </div>
                    <ul class="auth-visual__checklist">
                        <li><i class="fa-solid fa-circle-check"></i> Configurable grading & assessment engine</li>
                        <li><i class="fa-solid fa-circle-check"></i> Student, teacher & parent portals</li>
                        <li><i class="fa-solid fa-circle-check"></i> Built-in analytics & intervention tracking</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <div class="auth-form__inner">
                <div class="auth-form__heading">Register your school</div>
                <div class="auth-form__sub">Create your administrator account to get started</div>

                @if ($errors->any())
                    <div class="form-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-section-label">Administrator Details</div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <div class="form-input-wrap">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" name="name" class="form-input" placeholder="John Banda" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <div class="form-input-wrap">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" name="phone" class="form-input" placeholder="+265 ..." required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="form-input-wrap">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" class="form-input" placeholder="you@school.com" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="form-input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" class="form-input" placeholder="••••••••"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <div class="form-input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password_confirmation" class="form-input"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-label">School Details</div>
                    <div class="form-group">
                        <label class="form-label">School Name</label>
                        <div class="form-input-wrap">
                            <i class="fa-solid fa-school"></i>
                            <input type="text" name="school_name" class="form-input" placeholder="e.g. Kamuzu Academy"
                                required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Institution Type</label>
                            <div class="form-input-wrap">
                                <i class="fa-solid fa-building-columns"></i>
                                <select name="institution_type" class="form-select" required>
                                    <option value="" disabled selected>Select type</option>
                                    <option value="primary">Primary School</option>
                                    <option value="secondary">Secondary School</option>
                                    <option value="international">International School</option>
                                    <option value="college">College / Training Institution</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <div class="form-input-wrap">
                                <i class="fa-solid fa-globe"></i>
                                <input type="text" name="country" class="form-input" placeholder="Malawi" value="Malawi"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-terms">
                        <input type="checkbox" name="terms" required style="margin-top:2px;">
                        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                    </div>

                    <button type="submit" class="btn-primary">
                        Create Account <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-form__footer">
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>