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
    <div id="wrapper">
        @include('partials.stripe')

        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                    {!! municipio_get_logotype(!empty(get_field('404_error_logotype', 'options')) ? get_field('404_error_logotype', 'options') : 'standard')  !!}
                </div>
            </div>
        </div>
        @yield('content')
     </div>

    {!! wp_footer() !!}
</body>
</html>
