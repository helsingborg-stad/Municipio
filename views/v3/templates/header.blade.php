@header([
    'id' => 'site-header',
    'classList' => array_merge(
        ['site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : ''],
        isset($classList) ? (array) $classList : []
    ),
    'context' => 'site.header'
])
    @include('partials.header.skip-to-main-content')
    @includeWhen($hasMainMenu, 'partials.header.skip-to-main-menu')
    @includeWhen($hasSideMenu, 'partials.header.skip-to-side-menu')

    {{-- Search Form --}}
    @section('search-form')
        @includeWhen($showNavigationSearch || $showQuicklinksSearch, 'partials.search.search-modal')
    @show

    {{-- Navbars --}}
    @section('navigation')

        {{-- Top Navigation --}}
        @yield('top-navigation')

        {{-- Primary Navigation --}}
        @yield('primary-navigation')

        {{-- Secondary Navigation --}}
        @yield('secondary-navigation')

    @show
@endheader
