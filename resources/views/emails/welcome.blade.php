<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.email.welcome_title') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #333333;
        }
        .content {
            padding: 20px 0;
            color: #555555;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background-color: #3C50E0;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            color: #999999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('ui.email.welcome_heading') }}</h1>
        </div>
        <div class="content">
            <p>{{ __('ui.email.hi_name', ['name' => $user->name]) }}</p>
            <p>{{ __('ui.email.welcome_body') }}</p>
            <p>{{ __('ui.email.get_started') }}</p>
            <a href="{{ route('login') }}" class="button text-white">{{ __('ui.email.login_to_dashboard') }}</a>
        </div>
        <div class="footer">
            <p>&copy; {{ __('ui.email.copyright', ['year' => date('Y')]) }}</p>
        </div>
    </div>
</body>
</html>
