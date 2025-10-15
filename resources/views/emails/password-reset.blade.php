<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" media="screen">

    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            word-break: break-word;
        }
        .container {
            max-width: 480px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ea580c;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }
        .btn:hover {
            background-color: #f97316;
        }
        .btn-container {
            text-align: center;
            margin-top: 16px;
        }
        .logo {
            max-width: 100%;
            height: 36px;
            width: auto;
        }
        h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
        }
        p {
            margin: 8px 0;
            line-height: 1.6;
            color: #4b5563;
        }
        .thank-you {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .footer {
            font-size: 12px;
            color: #6b7280;
            margin-left: auto;
        }

        @media (max-width: 600px) {
            .container {
                padding: 16px;
                margin: 10px;
            }
            .logo {
                height: 32px;
            }
            .btn {
                display: block;
                width: 100%;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1f2937;
            }
            .container {
                background-color: #374151;
                box-shadow: none;
            }
            h2 {
                color: #e5e7eb;
            }
            p {
                color: #d1d5db;
            }
            .thank-you {
                border-top-color: #4b5563;
            }
            .footer {
                color: #9ca3af;
            }
            .btn {
                background-color: #f97316;
            }
            .btn:hover {
                background-color: #ea580c;
            }
        }
    </style>
</head>
<body>
    <div role="article" aria-roledescription="email" aria-label lang="en">
        <section class="container">
            <header>
                <a href="{{ config('app.url') }}" style="display: block; text-align: center;">
                    <img class="logo" src="{{ asset('img/logo.jpg') }}" alt="ET Mart" style="width: 300px; height: 200px;">
                </a>
            </header>
            <main style="margin-top: 24px; text-align: center;">
                <h2>Hello, {{ $user->name }}!</h2>
                <p>We received a request to reset your password for your ET Mart account. If you made this request, please click the button below to set a new password:</p>
                <div class="btn-container" style="margin: 10px auto;">
                    <a href="{{ $verificationUrl }}" class="btn" target="_blank" style="background-color: #1e87f0; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Reset Password</a>
                </div>
                <p style="font-size: 14px;">
                    This link is valid for 30 minutes. If you didn't request a password reset, you can safely ignore this email—your account is secure.

                    For any assistance, feel free to contact our support team.
                </p>
            </main>
            <div class="thank-you" style="text-align: center; margin-top: 20px;">
                <p>Thank you for shopping with ET Mart! We’re always here to make your experience better.</p>
            </div>
            <footer style="margin-top: 32px; text-align: center;">
                <p>Cheers,<br>The ET Mart Team</p>
                <sub>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</sub>
            </footer>
        </section>
    </div>
</body>
</html>
