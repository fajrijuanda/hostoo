<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Hostoo</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #181E4B;
            --secondary: #DF6951;
            --text-body: #5E6282;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-body);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .header {
            background: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo img {
            height: 40px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            padding: 40px;
        }

        h1 {
            color: var(--primary);
            font-size: 32px;
            margin-bottom: 10px;
            text-align: center;
        }

        .last-updated {
            text-align: center;
            font-size: 14px;
            color: #999;
            margin-bottom: 40px;
        }

        h2 {
            color: var(--primary);
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            display: inline-block;
        }

        p {
            margin-bottom: 15px;
        }

        ul {
            margin-bottom: 20px;
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }

        .cta-back {
            display: inline-block;
            margin-top: 40px;
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            border: 1px solid var(--secondary);
            padding: 10px 25px;
            border-radius: 50px;
            transition: 0.3s;
        }

        .cta-back:hover {
            background: var(--secondary);
            color: white;
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-bottom: 30px;
            font-size: 14px;
            color: #ccc;
        }
    </style>
</head>
<body>

    <header class="header">
        <a href="{{ route('welcome') }}" class="logo">
            <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Hostoo Logo">
        </a>
    </header>

    <div class="container">
        <h1>Privacy Policy</h1>
        <div class="last-updated">Last Updated: December 2025</div>

        <p>At Hostoo, we value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data when you use our hosting services.</p>

        <h2>1. Information We Collect</h2>
        <p>We collect information that you provide directly to us, including:</p>
        <ul>
            <li><strong>Account Information:</strong> Name, email address, phone number, and billing details.</li>
            <li><strong>Usage Data:</strong> Information about how you interact with our services, such as server logs, IP addresses, and browser type.</li>
            <li><strong>Communication:</strong> Records of your interactions with our support team.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <p>We use the collected information for the following purposes:</p>
        <ul>
            <li>To provide and maintain our hosting services.</li>
            <li>To process transactions and send related alerts (e.g., invoices, expiration notices).</li>
            <li>To improve our platform and user experience.</li>
            <li>To communicate with you regarding updates, security alerts, and support responses.</li>
        </ul>

        <h2>3. Data Protection</h2>
        <p>We implement industry-standard security measures to protect your data from unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.</p>

        <h2>4. Third-Party Sharing</h2>
        <p>We do not sell your personal data. We may share information with trusted third-party service providers who assist us in operating our website and conducting our business, such as payment processors (e.g., Midtrans) and email delivery services.</p>

        <h2>5. Your Rights</h2>
        <p>You have the right to access, correct, or delete your personal information. If you wish to exercise these rights, please contact our support team.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('welcome') }}" class="cta-back">&larr; Back to Home</a>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Hostoo Web Hosting. All rights reserved.
    </div>

</body>
</html>
