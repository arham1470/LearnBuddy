<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your LearnBuddy Verification Code</title>
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
            background-color: #FFF6EE;
            padding: 36px 32px 24px;
            text-align: center;
            border-bottom: 1px solid #FFE4D0;
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
            background-color: #FFE6D6;
            color: #FF5E00;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
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
            font-size: 20px;
            font-weight: 700;
            color: #1A1A1A;
            margin: 0 0 16px;
        }
        .text {
            font-size: 15px;
            color: #4A5568;
            margin: 0 0 20px;
        }
        .otp-box {
            background: linear-gradient(135deg, #FFF6EE 0%, #FFF0E2 100%);
            border: 2px dashed #FF5E00;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            margin: 28px 0;
        }
        .otp-label {
            font-size: 12px;
            font-weight: 700;
            color: #FF5E00;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }
        .otp-code {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 38px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #1A1A1A;
            margin: 0;
            padding-left: 10px;
        }
        .otp-expiry {
            font-size: 13px;
            color: #718096;
            margin-top: 8px;
        }
        .info-card {
            background-color: #F7FAFC;
            border-left: 4px solid #FF5E00;
            border-radius: 8px;
            padding: 14px 18px;
            margin: 24px 0;
            font-size: 13px;
            color: #4A5568;
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
                            <span class="badge">Email Verification</span>
                        </div>
                    </div>

                    <!-- Main Body -->
                    <div class="content">
                        <h1 class="greeting">Hello, {{ $user->name }}!</h1>
                        <p class="text">
                            Thank you for creating an account on <strong>LearnBuddy</strong>. To ensure the security of your account and complete your registration, please verify your email address using the one-time verification code below:
                        </p>

                        <!-- OTP Box -->
                        <div class="otp-box">
                            <div class="otp-label">Your 6-Digit OTP Code</div>
                            <div class="otp-code">{{ $otpCode }}</div>
                            <div class="otp-expiry">Valid for {{ $expiresInMinutes }} minutes</div>
                        </div>

                        <div class="info-card">
                            <strong>What happens next?</strong><br>
                            Once you enter this verification code, your email will be confirmed and your registration will be submitted to the LearnBuddy administrative team for approval. You will receive an email immediately once an administrator activates your account.
                        </div>

                        <p class="text" style="font-size: 13px; color: #718096; margin-bottom: 0;">
                            <strong>Security Notice:</strong> Never share this code with anyone. LearnBuddy support staff will never ask for your verification code. If you did not request this verification, please ignore this email.
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
