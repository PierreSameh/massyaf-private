<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e7f9f1;
            color: #1b5e20;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .success-icon {
            font-size: 100px;
        }
        .message {
            font-size: 24px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1b5e20;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <div class="message">Your payment was successful!</div>
        {{-- <a href="{{ url('/') }}" class="btn">Return to Home</a> --}}
    </div>
</body>
</html>
