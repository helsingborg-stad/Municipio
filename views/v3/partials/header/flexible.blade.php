@if (!empty($headerData))
    @php($logoScrollShrinkStyle = !empty($headerData['logoScrollShrinkEnabled']) ? '--municipio-header-logo-overlap-multiplier: ' . ($headerData['logoScrollShrinkOverlapMultiplier'] ?? 0.25) . '; --municipio-header-logo-scroll-aspect-ratio: ' . ($headerData['logoScrollShrinkAspectRatio'] ?? 1) . ';' : null)
    @php($logoScrollShrinkAttributes = !empty($headerData['logoScrollShrinkEnabled']) ? ['style' => $logoScrollShrinkStyle, 'data-municipio-logo-scroll-aspect-ratio' => (string) ($headerData['logoScrollShrinkAspectRatio'] ?? 1), 'data-municipio-logo-scroll-aspect-ratio-source' => (string) ($headerData['logoScrollShrinkAspectRatioSource'] ?? 'fallback')] : [])
    @include('partials.header.skip-to-main-content')
    @includeWhen($hasMainMenu, 'partials.header.skip-to-main-menu')
    @includeWhen($hasSideMenu, 'partials.header.skip-to-side-menu')

    @if(!empty($headerData['upperItems']))
        @scope(['name' => ['header-flexible-upper', 'header-flexible', 'header']])
            @header([
                'classList' => array_merge(
                    ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : '', !empty($headerData['logoScrollShrinkEnabled']) ? 'c-header--logotype-scroll-shrink' : ''],
                    $headerData['upperHeader']['classList'],
                    isset($classList) ? (array) $classList : [],
                    $classes ?? []
                ),
                'id' => 'site-header-flexible-upper',
                'sticky' => $headerData['upperHeader']['sticky'],
                'attributeList' => $logoScrollShrinkAttributes,
                'context' => 'site.header.flexible.upper'
            ])
                <div class="c-header__main-upper-area">
                    @foreach (['left', 'center', 'right'] as $alignment)
                        @include('partials.header.components.headerLoop', 
                            [
                                'area' => 'upper', 
                                'key' => 'upperItems', 
                                'align' => $alignment
                            ]
                        )
                    @endforeach
                </div>
                    @if ($headerData['upperHeader']['innerMegaMenu'])
                        @include('partials.navigation.megamenu')
                    @endif
            @endheader
       @endscope 
    @endif
    @if (!empty($headerData['lowerItems']))
        @scope(['name' => ['header-flexible-lower', 'header-flexible', 'header']])
            @header([
                'classList' => array_merge(
                    ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : '', !empty($headerData['logoScrollShrinkEnabled']) ? 'c-header--logotype-scroll-shrink' : ''],
                    $headerData['lowerHeader']['classList'],
                    isset($classList) ? (array) $classList : [],
                    $classes ?? []
                ),
                'id' => 'site-header-flexible-lower',
                'sticky' => $headerData['lowerHeader']['sticky'],
                'attributeList' => $logoScrollShrinkAttributes,
                'context' => 'site.header.flexible.lower',
            ])
                <div class="c-header__main-lower-area">
                    @foreach (['left', 'center', 'right'] as $alignment) 
                        @include('partials.header.components.headerLoop',
                            [
                                'area' => 'lower', 
                                'key' => 'lowerItems', 
                                'align' => $alignment
                            ]
                        )
                    @endforeach
                </div>
                @if ($headerData['lowerHeader']['innerMegaMenu'])
                    @include('partials.navigation.megamenu')
                @endif
            @endheader
        @endscope
    @endif

    @if(
        !empty($megaMenu['items']) &&
        $headerData['nonStickyMegaMenu']
    )
        @include('partials.navigation.megamenu')
    @endif
    @if ($headerData['hasSearch'])
        @include('partials.search.search-modal')
    @endif
@endif
