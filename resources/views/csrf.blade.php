<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index, follow">

    <meta name="keywords" content="{{ config('app.name') }}">
    <meta name="description" content={{ config('app.name') }}">
    <meta name="generator" content="{{ config('app.name') }}">
    <meta name="author" content="{{ config('app.name') . ' ' . date('Y') }}">
    <meta name="copyright" content="registered, delegated, verified {{ date('Y') }}">

    <link rel="canonical" href="{{ config('app.url') }}">
    <link rel="shortcut icon" href="/favicon.ico">
    <title>{{ config('app.name') }}</title>
</head>

<body>
    <section id="form">
        <div class="container">
            <header>
                <h2>{{ config('app.name') }}</h2>
                <p>{{ config('app.url') }}</p>
                <p>{{ Session::get('message') }}</p>
            </header>
            <form method="post" action="/test/csrf-post">
                <input required type="hidden" name="_token" value="{{ Session::token() }}" />
                <input type="text" name="token" value="{{ Session::token() }}" />
                <input required type="number" name="number" min="0" max="99" type="number" placeholder="number" value="0" />
                <input type="submit" value="submit" class="primary" />
            </form>
        </div>
    </section>
</body>
</html>
