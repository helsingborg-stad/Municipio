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

    {{-- Wordpress required call to wp_header() --}}
    {!! $wpHeader !!}

</head>

<body class="{{ $bodyClass }}">

    {{-- Site header --}}
    @includeIf('partials.header')

    <div class="">

        {{-- Before page layout --}}
        @yield('before-layout')

        {{-- Page layout --}}
        @section('layout')
            <section>
                @grid(["container" => true, 'classList' => ['u-margin__top--12']])
                    @grid([
                        "col" => [
                            "xs" => [1,13],
                            "sm" => [1,13],
                            "md" => [1,13],
                            "lg" => [3,9],
                            "xl" => [2,10]
                        ],
                        "row" => [
                            "xs" => [1,2],
                            "sm" => [1,2],
                            "md" => [1,2],
                            "lg" => [1,2],
                            "xl" => [1,2]
                        ]
                    ])
                        {{-- Above --}}
                        @hasSection('above')
                            @yield('above')
                        @endif
                    @endgrid
                @endgrid

                @grid(["container" => true, 'classList' => ['u-margin__top--8']])
                    @grid([
                            "col" => [
                                "xs" => [1,13],
                                "sm" => [1,13],
                                "md" => [1,13],
                                "lg" => [1,9],
                                "xl" => [1,10]
                            ],
                            "row" => [
                                "xs" => [1,2],
                                "sm" => [1,2],
                                "md" => [1,2],
                                "lg" => [1,2],
                                "xl" => [1,2]
                            ]
                        ])
                    {{-- Sidebar left --}} {{-- TODO: RENAME TO "SIDEBAR" --}}
                        @hasSection('sidebar-left')
                            @includeIf('partials.sidebar', ['id' => 'sidebar-left'])
                            @sidebar([
                                'logo' => $logotype->standard['url'],
                                'items' => $secondaryMenuItems
                            ])
                            @endsidebar
                        @endif
                    @endgrid

                {{-- Content --}}
                <!-- <div class="{{-- $layout['content'] --}} content"> -->
                    @grid([
                        "col" => [
                            "xs" => [1,13],
                            "sm" => [1,13],
                            "md" => [1,13],
                            "lg" => [3,9],
                            "xl" => [3,10]
                        ],
                        "row" => [
                            "xs" => [1,2],
                            "sm" => [1,2],
                            "md" => [1,2],
                            "lg" => [1,2],
                            "xl" => [1,2]
                        ]
                    ])
                    @yield('content')
                    @endgrid
                @endgrid

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

    </div>
    {{-- Wordpress required call --}}
</div>

@section('footer')
    @includeIf('partials.footer')
@show

{{-- Wordpress required call to wp_footer() --}}
{!! $wpFooter !!}

</body>
</html>