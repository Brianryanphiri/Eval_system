<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body style="font-family: Arial, sans-serif; background: #F5F6F8; padding: 30px 0; margin: 0;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden;">
        <div style="background: #0F0F0F; padding: 24px 32px;">
            <div style="color: #ffffff; font-weight: 800; font-size: 16px;">Pro Tech Solutions</div>
            <div style="color: #9CA3AF; font-size: 11px; letter-spacing: 0.06em;">EVALUATION SYSTEM</div>
        </div>

        <div style="padding: 32px;">
            <h2 style="margin: 0 0 8px; font-size: 19px; color: #111827;">Hello {{ $user->name }},</h2>
            <p style="color: #4B5563; font-size: 14px; line-height: 1.6;">
                An account has been created for you on {{ $user->school->name }}'s evaluation system.
                Use the details below to sign in for the first time.
            </p>

            <div style="background: #F5F6F8; border-radius: 10px; padding: 18px 20px; margin: 22px 0;">
                <div
                    style="font-size: 11px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                    Email</div>
                <div style="font-size: 14px; color: #111827; font-weight: 700; margin-bottom: 14px;">{{ $user->email }}
                </div>
                <div
                    style="font-size: 11px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                    Temporary Password</div>
                <div style="font-size: 14px; color: #111827; font-weight: 700; font-family: monospace;">
                    {{ $temporaryPassword }}</div>
            </div>

            <p style="color: #4B5563; font-size: 13px; line-height: 1.6;">
                You will be asked to set a new password the first time you sign in.
            </p>

            <a href="{{ route('login') }}"
                style="display: inline-block; margin-top: 10px; background: #0F0F0F; color: #ffffff; text-decoration: none; padding: 12px 22px; border-radius: 9px; font-size: 13px; font-weight: 700;">
                Sign In
            </a>
        </div>
    </div>
</body>

</html>