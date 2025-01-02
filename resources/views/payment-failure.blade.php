<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdecea;
            color: #b71c1c;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .error-icon {
            font-size: 100px;
        }
        .message {
            font-size: 24px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #b71c1c;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <div class="message">Your payment failed.</div>
        {{-- <a href="{{ url('/payment') }}" class="btn">Try Again</a> --}}
    </div>
</body>
</html>
