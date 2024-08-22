@if (!empty($headerData))
    @if(!empty($headerData['upperItems']))
    @header([
        'classList' => array_merge(
            ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
            isset($classList) ? (array) $classList : []
        ),
        'id' => 'site-header-flexible-upper',
        'backgroundColor' => $headerData['upperHeader']['backgroundColor'],
        'sticky' => $headerData['upperHeader']['sticky'],
        'context' => 'site.header.flexible.upper'
    ])
        <div class="c-header__main-upper-area-container">
            <div class="c-header__main-upper-area o-container">
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
        </div>
    @endheader
    @endif
    @if (!empty($headerData['lowerItems']))
        @header([
            'classList' => array_merge(
                ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
                isset($classList) ? (array) $classList : []
        ),
            'id' => 'site-header-flexible-lower',
            'backgroundColor' => $headerData['lowerHeader']['backgroundColor'],
            'sticky' => $headerData['lowerHeader']['sticky'],
            'context' => 'site.header.flexible.lower'
        ])
            <div class="c-header__main-lower-area-container">
                <div class="c-header__main-lower-area o-container">
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
            </div>
        @endheader
    @endif

    @if(!empty($megaMenuItems) && $headerData['hasMegaMenu'])
        @include('partials.navigation.megamenu')
    @endif
    @if ($headerData['hasSearch'])
        @include('partials.search.search-modal')
    @endif
@endif
