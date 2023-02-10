<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['title'] }}</title>
</head>
<body>
    <h3>{{ $data['user_name'] }} 様</h3>
    <br/>
    <p>{{ $data['system_name'] }}のユーザ登録が完了いたしました。</p>
    <p>{{ $data['system_name'] }}には{{ $data['start_date'] }}以降ログインできます。</p>
    <p>以下のID・仮パスワードで本システムへログイン後、パスワードの設定をお願いいたします。</p>
    <br/>
    <p>URL: {{ $data['url'] }}</p>
    <p>ログインID: {{ $data['login_id'] }}</p>
    <p>仮パスワード: {{ $data['passphrase'] }}</p>
    <br/>
    <p>お問い合わせは{{ $data['email_contact'] }}までお願い致します。</p>
    <p>※このメールは{{ $data['system_name'] }}より自動で送信されています。</p>
    <br/>
    <h2 style="margin-top:15px">本メールにお心当たりがない場合は、{{ $data['email_contact'] }}までご連絡をお願いいたします。</h2>
    <br/>
</body>
</html>