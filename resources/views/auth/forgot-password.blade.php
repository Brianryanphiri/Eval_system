<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Pro Tech Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --pts-black: #0F0F0F; --pts-green: #7AC142; --pts-green-dark: #5E9A31;
            --pts-grey: #6B7280; --pts-grey-light: #E5E7EB; --pts-white: #FFFFFF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { min-height: 100vh; background: var(--pts-black); display: flex; align-items: center; justify-content: center; padding: 20px; }

        .reset-card { width: 100%; max-width: 420px; background: var(--pts-white); border-radius: 16px; padding: 40px; }
        .reset-icon {
            width: 52px; height: 52px; border-radius: 12px; background: var(--pts-green-light); color: var(--pts-green-dark);
            display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 20px;
        }
        .reset-title { font-size: 21px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .reset-sub { font-size: 13.5px; color: var(--pts-grey); margin-bottom: 26px; line-height: 1.5; }

        .status-msg {
            background: var(--pts-green-light); color: var(--pts-green-dark); border: 1px solid var(--pts-green);
            padding: 12px 14px; border-radius: 9px; font-size: 13px; margin-bottom: 18px;
        }
        .form-error {
            background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA;
            padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px;
        }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .form-input-wrap { position: relative; }
        .form-input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--pts-grey); font-size: 14px; }
        .form-input {
            width: 100%; padding: 12px 14px 12px 40px; border: 1.5px solid var(--pts-grey-light); border-radius: 9px;
            font-size: 14px; color: #111827; outline: none;
        }
        .form-input:focus { border-color: var(--pts-green); box-shadow: 0 0 0 3px rgba(122,193,66,0.15); }

        .btn-primary {
            width: 100%; padding: 13px; background: var(--pts-black); color: #fff; border: none; border-radius: 9px;
            font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary:hover { background: var(--pts-green-dark); }

        .back-link { display: flex; align-items: center; justify-content: center; gap: 7px; margin-top: 22px; font-size: 13px; color: var(--pts-grey); text-decoration: none; }
        .back-link:hover { color: var(--pts-green-dark); }
    </style>
</head>
<body>

    <div class="reset-card">
        <div class="reset-icon"><i class="fa-solid fa-key"></i></div>
        <div class="reset-title">Forgot your password?</div>
        <div class="reset-sub">Enter the email associated with your account and we'll send you a link to reset your password.</div>

        @if (session('status'))
            <div class="status-msg">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="form-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="form-input-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" class="form-input" placeholder="you@school.com" required autofocus>
                </div>
            </div>
            <button type="submit" class="btn-primary">Send Reset Link <i class="fa-solid fa-paper-plane"></i></button>
        </form>

        <a href="{{ route('login') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Sign In</a>
    </div>

</body>
</html>