<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            background-color: #F9F9FE;
            text-align: center;
        }

        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 48px auto;
            max-width: 500px;
            padding: 32px;
        }

        .card .logo-image {
            max-width: 200px;
        }

        .card h3 {
            margin-bottom: 24px;
            font-weight: 700;
            font-size: 24px;
            display: block;
            margin-top: 0;
        }

        .card .paragraph {
            margin-bottom: 16px;
            font-weight: 400;
            font-size: 16px;
            display: block;
            margin-top: 0;
        }

        .card .link {
            margin-bottom: 16px;
            font-weight: 500;
            font-size: 16px;
            display: block;
            margin-top: 0;
        }
    </style>
</head>

<body>
    <div class="card">
        {{-- <img class="logo-image" src="{{ $message->embed(public_path('assets/logo.png')) }}" alt="Earnlah"> --}}

        <h3 class="title">Reset Password</h3>

        <p class="paragraph">You have requested to reset your password.
            You may click the link below to reset your account's password.</p>

        <p class="paragraph">You have 1 hour(s) before link become expired. Ignore this if you have not requested it.
        </p>

        <a class="link" href="{{ $reset_password_url }}" target="_blank">Click here to reset your password</a>
    </div>
</body>

</html>
