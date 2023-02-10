<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['title'] }}</title>
</head>
<body>
    <p>{{ $data['system_name'] }}からのお知らせです。</p>
    <br/>
    <p>{{ $data['user_name'] }}さん宛にシステムログイン用のパスワードを再発行しました。</p>
    <br/>
    <p>ログインパスワード：{{ $data['new_password'] }}</p>
    <br/>
    <p>{{ $data['system_name'] }}にログイン後、すぐにパスワードを変更してください。</p>
    <br/>
    <p>お問い合わせは<a href="mailto:{{ $data['mail_to'] }}" target="_blank">{{ $data['mail_to'] }}</a>までお願い致します。</p>
    <p>※このメールは{{ $data['system_name'] }}より自動で送信されています。<p>
</body>
</html>