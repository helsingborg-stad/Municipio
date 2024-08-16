@if (!empty($headerData))
    @if(!empty($headerData['upper']))
    @header([
        'classList' => array_merge(
            ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
            isset($classList) ? (array) $classList : []
        ),
        'id' => 'site-header-flexible-upper',
        'context' => 'site.header.flexible.upper'
    ])
        <div class="c-header__main-upper-area-container">
            <div class="c-header__main-upper-area o-container">   
                @include('partials.header.components.headerLoop', ['row' => 'upper', 'align' => 'left'])
                @include('partials.header.components.headerLoop', ['row' => 'upper', 'align' => 'center'])
                @include('partials.header.components.headerLoop', ['row' => 'upper', 'align' => 'right'])
            </div>
        </div>
    @endheader
    @endif
    @if (!empty($headerData['lower']))
        @header([
            'classList' => array_merge(
                ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
                isset($classList) ? (array) $classList : []
        ),
            'id' => 'site-header-flexible-lower',
            'context' => 'site.header.flexible.lower'
        ])
            <div class="c-header__main-lower-area-container">
                <div class="c-header__main-lower-area o-container">
                    @include('partials.header.components.headerLoop', ['row' => 'lower', 'align' => 'left'])
                    @include('partials.header.components.headerLoop', ['row' => 'lower', 'align' => 'center'])
                    @include('partials.header.components.headerLoop', ['row' => 'lower', 'align' => 'right'])
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
