<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your LearnBuddy Account Has Been Approved!</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F8F9FA;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1A1A1A;
            -webkit-text-size-adjust: 100%;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
        }
        .container {
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #ECECEC;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .header {
            background-color: #ECFDF5;
            padding: 36px 32px 24px;
            text-align: center;
            border-bottom: 1px solid #D1FAE5;
        }
        .logo-text {
            font-size: 26px;
            font-weight: 800;
            color: #1A1A1A;
            letter-spacing: -0.5px;
            text-decoration: none;
        }
        .logo-accent {
            color: #FF5E00;
        }
        .badge {
            display: inline-block;
            background-color: #10B981;
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .content {
            padding: 36px 32px;
            color: #333333;
            line-height: 1.6;
        }
        .greeting {
            font-size: 22px;
            font-weight: 800;
            color: #1A1A1A;
            margin: 0 0 16px;
        }
        .text {
            font-size: 15px;
            color: #4A5568;
            margin: 0 0 20px;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn-cta {
            display: inline-block;
            background-color: #FF5E00;
            color: #FFFFFF !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 999px;
            box-shadow: 0 4px 14px rgba(255, 94, 0, 0.35);
        }
        .checklist-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 20px 24px;
            margin: 24px 0;
        }
        .checklist-title {
            font-size: 13px;
            font-weight: 700;
            color: #1A1A1A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .checklist-item {
            font-size: 14px;
            color: #4A5568;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        .footer {
            background-color: #FAFAFA;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #EEEEEE;
            font-size: 12px;
            color: #8C8C8C;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" bgcolor="#F8F9FA" cellpadding="0" cellspacing="0" style="padding: 40px 16px;">
        <tr>
            <td align="center">
                <div class="container">
                    <!-- Brand Header -->
                    <div class="header">
                        <a href="{{ config('app.url') }}" class="logo-text">
                            Learn<span class="logo-accent">Buddy</span>
                        </a>
                        <div>
                            <span class="badge">Account Approved</span>
                        </div>
                    </div>

                    <!-- Main Body -->
                    <div class="content">
                        <h1 class="greeting">Welcome, {{ $user->name }}! 🎉</h1>
                        <p class="text">
                            Great news! Your <strong>LearnBuddy</strong> account has been reviewed and officially approved by our administrator. Your student account is now fully active and ready for use.
                        </p>

                        <!-- CTA Button -->
                        <div class="cta-container">
                            <a href="{{ $loginUrl }}" class="btn-cta">
                                Sign In to LearnBuddy &rarr;
                            </a>
                        </div>

                        <!-- What you can do now -->
                        <div class="checklist-box">
                            <div class="checklist-title">What you can do now:</div>
                            <div class="checklist-item">&bull; Explore comprehensive courses in Web Development, Business, IT, and more.</div>
                            <div class="checklist-item">&bull; Enroll in live, on-demand, or physical classroom batches across Sri Lanka.</div>
                            <div class="checklist-item">&bull; Access lesson resources, quizzes, assignments, and verified certificates.</div>
                            <div class="checklist-item">&bull; Pay securely with PayHere, LankaQR, Visa, Mastercard, or local bank transfers.</div>
                        </div>

                        <p class="text" style="font-size: 13px; color: #718096; margin-bottom: 0;">
                            If you have questions or need assistance getting started, feel free to reply directly to this email or reach us anytime at <a href="mailto:support@learnbuddy.lk" style="color: #FF5E00; text-decoration: none;">support@learnbuddy.lk</a>.
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p style="margin: 0 0 6px;">
                            &copy; {{ date('Y') }} LearnBuddy Sri Lanka. All rights reserved.
                        </p>
                        <p style="margin: 0;">
                            Empowering modern learners across Sri Lanka &bull; <a href="mailto:support@learnbuddy.lk" style="color: #FF5E00; text-decoration: none;">support@learnbuddy.lk</a>
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
