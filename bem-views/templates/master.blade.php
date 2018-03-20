<!DOCTYPE html>
<html {!! $languageAttributes !!}>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">
    <meta charset="utf-8">

    <title>{{$wpTitle}}</title>

    <meta name="description" content="{{ $description }}" />
    <meta name="pubdate" content="{{ $published }}">
    <meta name="moddate" content="{{ $modified }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true" />

    <script> var ajaxurl = '{!! $ajaxUrl !!}';</script>

    {!! wp_head() !!}

</head>
<body class="{{ $bodyClass }}">

    @section('site-header')
        @include('partials.header')
    @show

    <main id="main">
        @section('site-body')
            <div class="container">
                @yield('content')
            </div>
        @show
    </main>

    @section('site-footer')
        @include('partials.footer')
    @show

    {!! wp_footer() !!}

</body>
</html>
