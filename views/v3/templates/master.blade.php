<!DOCTYPE html>
<html {!! $languageAttributes !!}>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $pageTitle }}</title>

    <meta name="pubdate" content="{{ $pagePublished }}">
    <meta name="moddate" content="{{ $pageModified }}">

    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true"/>

    <script>
        var ajaxurl = '{!! $ajaxUrl !!}';
    </script>

    {{-- Wordpress required call --}}
    {!! wp_head() !!}

</head>

<body class="{{ $bodyClass }}">

    {{-- Site header --}}
    @includeIf('partials.header')


    <div class="container--doc l-docs--content">

        {{-- Before page layout --}}
        @yield('before-layout')

        {{-- Page layout --}}
        @section('layout')
            <section>
                {{-- Above --}}
                @hasSection('above')
                    @yield('above')
                @endif

                {{-- Sidebar left --}} {{-- TODO: RENAME TO "SIDEBAR" --}}
                @hasSection('sidebar-left')
                    @includeIf('partials.sidebar', ['id' => 'sidebar-left'])
                    @sidebar([
                        'logo' => $logotype->standard['url'],
                        'items' => $sideNavigation
                    ])
                    @endsidebar
                @endif

                {{-- Content --}}
                <!-- <div class="{{-- $layout['content'] --}} content"> -->

                @yield('content')

                <!-- FAB -->
                @fab([
                    'position' => 'bottom-right',
                    'spacing' => 'lg',
                    'classList' => ['c-fab--show-on-scroll', 'u-visibility--hidden']
                ])

                    @button([
                        'type' => 'filled',
                        'icon' => 'close',
                        'size' => 'lg',
                        'text' => 'To the top',
                        'color' => 'primary',
                        'icon' => 'keyboard_arrow_up',
                        'reversePositions' => true

                    ])
                    @endbutton

                @endfab

            </section>

            {{-- Below --}}
            @hasSection('below')
                @yield('below')
            @endif


        @show

        {{-- After page layout --}}
        @yield('after-layout')

        {{-- Site footer --}}
        @section('footer')
            @includeIf('partials.footer')
        @show

    </div>



    {{-- Wordpress required call --}}
    {!! wp_footer() !!}


</body>
</html>