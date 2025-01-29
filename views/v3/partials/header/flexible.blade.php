@if (!empty($headerData))
    @if(!empty($headerData['upperItems']))
    @header([
        'classList' => array_merge(
            ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
            $headerData['upperHeader']['classList'],
            isset($classList) ? (array) $classList : []
        ),
        'id' => 'site-header-flexible-upper',
        'backgroundColor' => $headerData['upperHeader']['backgroundColor'],
        'sticky' => $headerData['upperHeader']['sticky'],
        'context' => 'site.header.flexible.upper'
    ])
        <div class="c-header__main-upper-area-container">
            @element([
                'baseClass' => 'o-container',
                'classList' => ['c-header__main-upper-area', 'o-container'],
                'context' => ['site.header.flexible-container-upper', 'site.header.flexible-container', 'site.header.container']
            ])
                @foreach (['left', 'center', 'right'] as $alignment)
                    @include('partials.header.components.headerLoop', 
                        [
                            'area' => 'upper', 
                            'key' => 'upperItems', 
                            'align' => $alignment
                        ]
                    )
                @endforeach
            @endelement
        </div>
            @if ($headerData['upperHeader']['innerMegaMenu'])
                @include('partials.navigation.megamenu')
            @endif
    @endheader
    @endif
    @if (!empty($headerData['lowerItems']))
        @header([
            'classList' => array_merge(
                ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
                $headerData['lowerHeader']['classList'],
                isset($classList) ? (array) $classList : []
        ),
            'id' => 'site-header-flexible-lower',
            'backgroundColor' => $headerData['lowerHeader']['backgroundColor'],
            'sticky' => $headerData['lowerHeader']['sticky'],
            'context' => 'site.header.flexible.lower'
        ])
            <div class="c-header__main-lower-area-container">
                @element([
                    'baseClass' => 'o-container',
                    'classList' => ['c-header__main-lower-area', 'o-container'],
                    'context' => ['site.header.flexible-container-lower', 'site.header.flexible-container', 'site.header.container']
                ])
                    @foreach (['left', 'center', 'right'] as $alignment) 
                        @include('partials.header.components.headerLoop',
                            [
                                'area' => 'lower', 
                                'key' => 'lowerItems', 
                                'align' => $alignment
                            ]
                        )
                    @endforeach
                @endelement
            </div>
            @if ($headerData['lowerHeader']['innerMegaMenu'])
                @include('partials.navigation.megamenu')
            @endif
        @endheader
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
