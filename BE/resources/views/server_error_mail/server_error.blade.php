<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['title'] }}</title>
</head>

<body>
    <p><b>環境</b> : {{ $data['env'] ?? '' }}</p>
    <b>{{ $data['content_title'] ?? '' }}</b>
    <br>{{ $data['day_error'] ?? '' }}
    @if (isset($data['type_error']) && $data['type_error'])
    <br>エラーレベル : {{ $data['type_error'] }}
    @endif
    <br>Log Level : {{ $data['error_level'] ?? '' }}
    <br>Process ID : {{ $data['process_id'] ?? '' }}<br>
    {!! $data['content'] ?? '' !!}
</body>
</html>
