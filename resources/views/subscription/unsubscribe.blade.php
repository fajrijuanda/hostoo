<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - Hostoo</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #181E4B;
            --secondary: #DF6951;
            --accent: #F1A501;
            --text-body: #5E6282;
            --light-bg: #FFF1DA;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            width: 100%;
            padding: 20px 0;
            text-align: center;
            background: transparent;
        }

        .logo img {
            height: 50px;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
        }

        .illustration {
            width: 180px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        h1 {
            color: var(--primary);
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 30px;
            font-size: 14px;
        }

        .options {
            text-align: left;
            margin-bottom: 20px;
        }

        .option-item {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .option-item input[type="radio"] {
            margin-right: 10px;
            accent-color: var(--secondary);
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .option-item label {
            cursor: pointer;
            font-size: 14px;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            resize: none;
            margin-top: 10px;
            outline: none;
            transition: border-color 0.3s;
        }

        textarea:focus {
            border-color: var(--secondary);
        }

        .btn {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #c64d36;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: transparent;
            color: #999;
            margin-top: 15px;
            display: inline-block;
            text-decoration: none;
            font-size: 14px;
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
        <div class="card">
            <!-- Using existing image as fallback since generation failed -->
            <img src="{{ Vite::asset('resources/images/avatar-mike.png') }}" alt="We are sad to see you go" class="illustration">
            
            <h1>We're sorry to see you go</h1>
            <p>Please let us know why you are unsubscribing. This helps us improve.</p>

            <form action="{{ route('subscribe.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ request('email') }}">

                <div class="options">
                    <div class="option-item">
                        <input type="radio" id="r1" name="reason" value="Too many emails" required>
                        <label for="r1">I receive too many emails</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="r2" name="reason" value="Not relevant">
                        <label for="r2">Content is not relevant to me</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="r3" name="reason" value="Found another provider">
                        <label for="r3">I found another provider</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="r4" name="reason" value="Other">
                        <label for="r4">Other reason</label>
                    </div>
                    
                    <textarea name="custom_reason" rows="3" placeholder="Tell us more (optional)..."></textarea>
                </div>

                <button type="submit" class="btn">Unsubscribe</button>
                <a href="{{ route('welcome') }}" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>

</body>
</html>
