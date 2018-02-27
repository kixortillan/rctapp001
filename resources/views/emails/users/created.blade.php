<html>

<body>
Hello

Click the button below to verify your registration.

<a href="{{ url('register/verify') . '?token=' . $user->verify_token }}">Get Started</a>

Thanks,<br>
{{ config('app.name') }}
</body>
</html>
