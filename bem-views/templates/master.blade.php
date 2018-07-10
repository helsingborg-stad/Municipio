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

    {{-- Site header --}}
    @section('header')
        @if (isset($headerLayout['template']))
            @includeIf('partials.header.' . $headerLayout['template'])
        @endif
    @show

    <main id="main" class="c-main s-main">
        {{-- Before page layout --}}
        @yield('before-layout')

        {{-- Page layout --}}
        @section('layout')
            <div class="container">
                <div class="grid grid--columns">
                    {{-- Above --}}
                    @hasSection('above')
                        <div class="grid-xs-12 s-above">
                            @yield('above')
                        </div>
                    @endif

                    {{-- Sidebar left --}}
                    @hasSection('sidebar-left')
                        <div class="{{$layout['sidebarLeft']}} s-sidebar-left">
                            @yield('sidebar-left')
                        </div>
                    @endif

                    {{-- Content --}}
                    <div class="{{$layout['content']}} s-content">
                        @yield('content')
                    </div>

                    {{-- Sidebar right --}}
                    @hasSection('sidebar-right')
                        <div class="{{$layout['sidebarRight']}} s-sidebar-right">
                            @yield('sidebar-right')
                        </div>
                    @endif

                    {{-- Below --}}
                    @hasSection('below')
                        <div class="grid-xs-12 s-below">
                            @yield('below')
                        </div>
                    @endif
                </div>
            </div>
        @show

        {{-- After page layout --}}
        @yield('after-layout')
    </main>

    {{-- Site footer --}}
    @section('footer')
        @if (isset($footerLayout['template']))
            @includeIf('partials.footer.' . $footerLayout['template'])
        @endif
    @show

    {!! wp_footer() !!}

</body>
</html>
