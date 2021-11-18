<!DOCTYPE html>
<html {!! $languageAttributes !!}>
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{!! $pageTitle !!}</title>

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

    <body class="{{ $bodyClass }}" js-page-id="{{$pageID}}">
        <div class="site-wrapper">
            
            {{-- Site header --}}
            @section('site-header')
                @if (!empty($customizer->headerApperance))
                    @includeIf('partials.header.' . $customizer->headerApperance)
                @endif
            @show

            {{-- Notices Notice::add() --}}
            @if($notice) 
                @foreach ($notice as $noticeItem)
                    @notice($noticeItem)
                    @endnotice
                @endforeach
            @endif

            {{-- Before page layout --}}
            @yield('before-layout')

            {{-- Page layout --}}
            <main>
                @section('layout')
                    <div class="o-container">

                        @hasSection('helper-navigation')
                            <div class="o-grid o-grid--no-margin u-print-display--none">
                                <div class="o-grid-12">
                                    @yield('helper-navigation')
                                </div>
                            </div>
                        @endif

                        @hasSection('above')
                            <div class="o-grid u-print-display--none">
                                <div class="o-grid-12">
                                    @yield('above')
                                </div>
                            </div>
                        @endif

                        <!--  Main content padder -->
                        <div class="u-padding__x--{{ $mainContentPadding['md'] }}@md u-padding__x--{{ $mainContentPadding['lg'] }}@lg">
                            <div class="o-grid o-grid--nowrap@lg o-grid--nowrap@md">
                                @hasSection('sidebar-left')
                                    <div class="o-grid-12 o-grid-{{$leftColumnSize}}@md o-order-2 o-order-1@md u-print-display--none">
                                        @yield('sidebar-left')
                                    </div>
                                @endif
                        
                                <div class="o-grid-12 o-grid-auto@md o-order-1 o-order-2@md">
                                    @yield('content')
                                </div>

                                @hasSection('sidebar-right')
                                    <div class="o-grid-12 o-grid-{{$rightColumnSize}}@md o-order-3 o-order-3@md u-print-display--none">
                                        @yield('sidebar-right')
                                    </div>
                                @endif
                            </div>
                        </div>

                        @hasSection('below')
                            <div class="o-grid">
                                <div class="o-grid-12">
                                    @yield('below')
                                </div>
                            </div>
                        @endif
                    </div>
                @show
            </main>

            {{-- After page layout --}}
            @yield('after-layout')

        </div>

        @section('footer')
            @includeIf('partials.footer')
        @show

        {{-- Floating menu --}}
        @includeWhen($floatingMenuItems, 'partials.navigation.floating')

        {{-- Wordpress required call to wp_footer() --}}
        {!! $wpFooter !!}

    </body>
</html>