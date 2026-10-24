<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Tech Solutions – verification code</title>
    <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
    <style>
        /* Client-specific resets */
        body,
        table,
        td,
        p,
        a,
        div,
        span {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: collapse;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }

        /* Ensure full-width background and no unwanted spacing */
        body {
            margin: 0;
            padding: 0;
            background-color: #F5F6F8;
            width: 100% !important;
            height: 100% !important;
        }

        /* Make the outer table take full width */
        .wrapper {
            width: 100%;
            background-color: #F5F6F8;
            padding: 32px 0;
        }

        /* Card styling */
        .card {
            width: 100%;
            max-width: 480px;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02);
        }

        /* Header styling */
        .header {
            background-color: #0F0F0F;
            padding: 20px 28px;
        }

        .header-title {
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.4;
            letter-spacing: 0.01em;
            margin: 0;
        }

        .header-sub {
            color: #9CA3AF;
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin: 4px 0 0 0;
        }

        /* Body content */
        .content {
            padding: 28px;
        }

        .message {
            font-size: 14px;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0 0 16px 0;
        }

        .code-box {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 0.15em;
            color: #0F0F0F;
            background-color: #F5F6F8;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            margin-bottom: 16px;
            font-family: 'Courier New', Courier, monospace;
            word-break: break-word;
            line-height: 1.2;
        }

        .note {
            font-size: 12.5px;
            color: #6B7280;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
        }

        /* Responsive adjustments */
        @media only screen and (max-width: 520px) {
            .wrapper {
                padding: 20px 0 !important;
            }

            .card {
                max-width: 100% !important;
                border-radius: 8px !important;
            }

            .header {
                padding: 16px 20px !important;
            }

            .content {
                padding: 24px 20px !important;
            }

            .code-box {
                font-size: 28px !important;
                padding: 14px !important;
                letter-spacing: 0.1em !important;
            }

            .message {
                font-size: 13.5px !important;
            }

            .note {
                font-size: 12px !important;
            }
        }

        /* For Outlook (desktop) — ensure the code box doesn't stretch oddly */
        .code-box {
            mso-line-height-rule: exactly;
        }
    </style>
</head>

<body style="margin:0; padding:0; background:#F5F6F8; font-family: Arial, Helvetica, sans-serif;">

    <!-- Outer wrapper with full-width background -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="wrapper"
        style="background:#F5F6F8; padding:32px 0;">
        <tr>
            <td align="center" style="padding:0 16px;">
                <!-- Main card -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="card"
                    style="max-width:480px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.02);">

                    <!-- Header -->
                    <tr>
                        <td class="header" style="background:#0F0F0F; padding:20px 28px;">
                            <p class="header-title"
                                style="color:#ffffff; font-size:15px; font-weight:700; font-family: Arial, Helvetica, sans-serif; line-height:1.4; letter-spacing:0.01em; margin:0;">
                                Pro Tech Solutions</p>
                            <p class="header-sub"
                                style="color:#9CA3AF; font-size:11px; font-family: Arial, Helvetica, sans-serif; letter-spacing:0.06em; text-transform:uppercase; margin:4px 0 0 0;">
                                Evaluation System</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="content" style="padding:28px;">
                            <p class="message"
                                style="font-size:14px; color:#111827; font-family: Arial, Helvetica, sans-serif; line-height:1.5; margin:0 0 16px 0;">
                                Use the code below to finish signing in. It expires in 10 minutes.
                            </p>

                            <!-- Code block -->
                            <div class="code-box"
                                style="font-size:32px; font-weight:800; letter-spacing:0.15em; color:#0F0F0F; background:#F5F6F8; border-radius:10px; padding:16px; text-align:center; margin-bottom:16px; font-family: 'Courier New', Courier, monospace; word-break:break-word; line-height:1.2;">
                                {{ $code }}
                            </div>

                            <p class="note"
                                style="font-size:12.5px; color:#6B7280; font-family: Arial, Helvetica, sans-serif; line-height:1.5; margin:0;">
                                If you did not try to sign in, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Main card -->
            </td>
        </tr>
    </table>
    <!-- /Outer wrapper -->

</body>

</html>