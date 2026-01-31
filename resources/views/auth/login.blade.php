<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CV Analyzer | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('assets/css/login_page.css') }}">
</head>

<body>
    <div class="login-container">
        <!-- LEFT SIDE -->
        <div class="left-panel">
            <div class="brand">
                CV<br>Analyzer
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right-panel">
            <div class="welcome">Welcome Back</div>
            <div class="company">CV Analyzer</div>
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="input-group">
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>
    </div>
</body>

</html>
