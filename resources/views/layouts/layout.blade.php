<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset=utf-8>
    <meta name="viewport" content="user-scalable=0,width=device-width,minimum-scale=1,initial-scale=1,maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trivija') }}</title>

    <link href="{{ asset('/css/trivia.css') }}" rel="stylesheet">
</head>
<body>

<div class="page-wrapper">
    @include('includes.alerts')
    @yield('content')
</div>

</body>
</html>