<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $schoolName }}</title>
</head>

<body
    style="font-family: Arial, sans-serif; font-size: 14px; color: #111827; line-height: 1.6; padding: 20px; background: #F5F6F8;">

    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background: #0F0F0F; color: #ffffff; padding: 18px 24px; font-size: 16px; font-weight: bold;">
                {{ $schoolName }}
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">

                @if ($guardianName)
                    <p>Dear {{ $guardianName }},</p>
                @endif

                <div style="white-space: pre-line;">{{ $bodyText }}</div>

                <p style="margin-top: 24px; font-size: 12px; color: #6B7280;">
                    {{ $classroom->name }} &middot; {{ $termName }} &middot; {{ $academicYear }}
                </p>

                <p style="margin-top: 16px;">
                    {{ $schoolName }}
                </p>

            </td>
        </tr>
    </table>

</body>

</html>