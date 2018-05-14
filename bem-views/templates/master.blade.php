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

    @section('header')
        @include('partials.header')
    @show

    <main id="main" class="c-main s-main">

        @yield('before-layout')

        @section('layout')
            <div class="container">
                <div class="grid">

                    @hasSection('above')
                        <div class="grid-xs-12 s-above">
                            @yield('above')
                        </div>
                    @endif

                    @hasSection('sidebar-left')
                        <div class="{{$layout['sidebarLeft']}} s-sidebar-left">
                            @yield('sidebar-left')
                        </div>
                    @endif

                    <div class="{{$layout['content']}} s-content">
                        @yield('content')
                    </div>

                    @hasSection('sidebar-right')
                        <div class="{{$layout['sidebarRight']}} s-sidebar-right">
                            @yield('sidebar-right')
                        </div>
                    @endif

                    @hasSection('below')
                        <div class="grid-xs-12 s-below">
                            @yield('below')
                        </div>
                    @endif

                </div>
            </div>
        @show

        @yield('after-layout')

    </main>

    @section('footer')
        @include('partials.footer')
    @show

    {!! wp_footer() !!}

</body>
</html>
