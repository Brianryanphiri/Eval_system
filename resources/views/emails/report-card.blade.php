<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #F5F6F8;
            padding: 24px;
            color: #111827;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 28px;
            max-width: 560px;
            margin: 0 auto;
        }

        .brand {
            font-size: 13px;
            font-weight: 700;
            color: #5E9A31;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        p {
            font-size: 14px;
            line-height: 1.6;
            color: #374151;
        }

        .note {
            border-left: 3px solid #7AC142;
            padding: 8px 14px;
            background: #F5F6F8;
            border-radius: 6px;
            margin: 14px 0;
            color: #111827;
        }

        .cta {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 16px;
            background: #0F0F0F;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .footer {
            font-size: 11px;
            color: #6B7280;
            margin-top: 24px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="brand">Pro Tech Solutions · Evaluation System</div>
        <h1>Report Card: {{ $student->full_name }}</h1>
        <p>Dear guardian,</p>
        <p>
            Please find attached the report card for <strong>{{ $student->full_name }}</strong>
            ({{ $student->student_number }}).
        </p>

        @if ($customMessage)
            <div class="note">{{ $customMessage }}</div>
        @endif

        <p>
            If you have any questions about this report, please contact the school office.
        </p>

        <div class="footer">
            Sent on {{ now()->format('d M Y') }} · {{ $student->school->name ?? '' }}
        </div>
    </div>
</body>

</html>