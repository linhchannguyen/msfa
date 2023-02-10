<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['title'] }}</title>
</head>
<body>
    <p>{{ $data['user_name_receive'] }}様</p>
    <br/>
    <p>ナレッジID：{{ $data['knowledge_id'] }}に{{$data['user_name_login']}}様より要望・要求事項が登録されました。</p>
    <br/>
    <p>【要望・要求事項】</p>
    <p>{{ $data['demand_note'] }}</p>
    <br/>
    <p>※このメールは送信専用のメールアドレスからお送りしています。<p>
    <p>  ご返信いただいても回答はできませんので、あらかじめご了承ください。<p>
</body>
</html>