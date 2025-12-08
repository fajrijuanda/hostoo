<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>New Plan Alert</title>
    <style>
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }
        
        body {
            font-family: 'Poppins', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .header {
            padding: 40px 0;
            text-align: center;
            background-color: #ffffff;
        }

        .content {
            padding: 0 40px 40px 40px;
            text-align: center;
            color: #5e6282;
        }

        h1 {
            color: #181e4b;
            margin: 0 0 20px 0;
            font-size: 28px;
            font-weight: 700;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
            color: #5e6282;
        }

        .plan-card {
            background-color: #f9f9fb;
            border: 1px solid #eeeeee;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }

        .plan-name {
            color: #181e4b;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .plan-price {
            color: #FF7D68;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 15px;
        }
        
        .plan-desc {
            color: #666;
            font-style: italic;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(180deg, #ff946d 0%, #ff7d68 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 34px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(255, 125, 104, 0.3);
        }

        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }

        /* -------------------------------------
           DARK MODE SUPPORT
        ------------------------------------- */
        .logo-light { display: block; height: 50px; margin: 0 auto; }
        .logo-dark { display: none; height: 50px; margin: 0 auto; }

        @media (prefers-color-scheme: dark) {
            body { background-color: #1a1a1a !important; }
            .container { background-color: #2d2d2d !important; box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important; }
            .header { background-color: #2d2d2d !important; }
            h1 { color: #f1f5f9 !important; }
            p { color: #a0a0a0 !important; }
            .content { color: #a0a0a0 !important; }
            .plan-card { background-color: #383838 !important; border-color: #444 !important; }
            .plan-name { color: #ffffff !important; }
            .plan-desc { color: #cccccc !important; }
            .footer { background-color: #252525 !important; border-top: 1px solid #333 !important; color: #777 !important; }
            
            /* Logo Switching */
            .logo-light { display: none !important; }
            .logo-dark { display: block !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Light Mode Logo -->
            <img src="{{ asset('images/logo.png') }}" alt="Hostoo" class="logo-light">
            <!-- Dark Mode Logo -->
            <img src="{{ asset('images/dark-logo.png') }}" alt="Hostoo" class="logo-dark">
        </div>
        
        <div class="content">
            <h1>New Plan Alert! &#128640;</h1>
            
            <p>Hello,</p>
            <p>We are excited to announce a new hosting plan that might be perfect for your needs!</p>
            
            <div class="plan-card">
                <div class="plan-name">{{ $planName }}</div>
                <div class="plan-price">Rp {{ $planPrice }}</div>
                <div class="plan-desc">{{ $planDescription }}</div>
                <a href="{{ url('/') }}" class="btn">Check It Out</a>
            </div>

            <p>Don't miss out on this opportunity to upgrade your digital presence.</p>
            
            <p style="margin-bottom: 0;">Best regards,<br>
            <strong style="color: #FF7D68;">The Hostoo Team</strong></p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Hostoo. All rights reserved.<br>
            <a href="{{ route('subscribe.unsubscribe', ['email' => $email]) }}" style="color: #999; text-decoration: none;">Unsubscribe</a> | <a href="{{ route('privacy') }}" style="color: #999; text-decoration: none;">Privacy Policy</a>
        </div>
    </div>
</body>
</html>
