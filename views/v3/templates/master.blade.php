<!DOCTYPE html>
<html {!! $languageAttributes !!}>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{!! $pageTitle !!}</title>

    <meta name="pubdate" content="{{ $pagePublished }}">
    <meta name="moddate" content="{{ $pageModified }}">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true">

    <script>
        var ajaxurl = '{!! $ajaxUrl !!}';
    </script>

    @if ($structuredData)
        <script type="application/ld+json">
        {!! $structuredData !!}
        </script>
    @endif

    {{-- Wordpress required call to wp_header() --}}
    {!! $wpHeader !!}

</head>

<body class="{{ $bodyClass }}" data-js-page-id="{{ $pageID }}" data-js-post-type="{{ $postType }}"
    @if ($customizer->headerSticky === 'sticky') data-js-toggle-item="mega-menu"
            data-js-toggle-class="mega-menu-open" @endif>
    <div class="site-wrapper">

        {{-- Site banner --}}
        @section('site-banner')
            @includeIf('partials.sidebar', ['id' => 'header-area-site-banner', 'classes' => []])
        @show

        {{-- Site header --}}
        @section('site-header')
            @if (!empty($customizer->headerApperance))
                @includeIf('partials.header.' . $customizer->headerApperance)
            @endif
        @show

        @includeWhen(!$helperNavBeforeContent, 'partials.navigation.helper', [
            'classList' => ['screen-reader-text'],
        ])
        {{-- Hero area and top sidebar --}}
        @hasSection('hero-top-sidebar')
            @yield('hero-top-sidebar')
        @endif

        {{-- Notices Notice::add() --}}
        @if ($notice)
            @foreach ($notice as $noticeItem)
                @notice($noticeItem)
                @endnotice
            @endforeach
        @endif

        {{-- Before page layout --}}
        @yield('before-layout')

        {{-- Page layout --}}
        <main id="main-content">
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
                    <div
                        class="u-padding__x--{{ $mainContentPadding['md'] }}@lg u-padding__x--{{ $mainContentPadding['lg'] }}@lg u-padding__x--{{ $mainContentPadding['lg'] }}@xl u-margin__bottom--12">
                        <div class="o-grid o-grid--nowrap@lg o-grid--nowrap@xl">

                            @hasSection('sidebar-left')
                                <div
                                    class="o-grid-12 o-grid-{{ $leftColumnSize }}@lg o-grid-{{ $leftColumnSize }}@xl o-order-2 o-order-1@lg o-order-1@xl u-print-display--none">
                                    @yield('sidebar-left')
                                </div>
                            @endif

                            <div
                                class="o-grid-12 o-grid-auto@lg o-grid-auto@xl o-order-1 o-order-2@lg o-order-2@xl u-display--flex u-flex--gridgap  u-flex-direction--column">
                                @yield('content')
                                @yield('content.below')
                            </div>

                            @hasSection('sidebar-right')
                                <div
                                    class="o-grid-12 o-grid-{{ $rightColumnSize }}@lg o-grid-{{ $rightColumnSize }}@xl o-order-3 o-order-3@lg o-order-3@xl u-print-display--none">
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
    @includeWhen(!empty($floatingMenu['items']), 'partials.navigation.floating')

    {{-- Wordpress required call to wp_footer() --}}
    {!! $wpFooter !!}

</body>

</html>
