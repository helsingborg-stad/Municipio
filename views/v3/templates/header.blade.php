@header([
    'id' => 'site-header',
    'classList' => [
        'site-header', isset($classList) ? is_array($classList) ? implode(' ', $classList) : $classList : '',
        $customizer->hamburgerMenuMobile ? 'hamburger-menu-mobile' : '',
    ],
    'context' => 'site.header'
])

    @include('partials.header.skip-to-main-content')
    @include('partials.header.skip-to-main-menu')
    @if($hasSideMenu)
        @include('partials.header.skip-to-side-menu')
    @endif

    {{-- Search Form --}}
    @section('search-form')
        @includeWhen($showNavigationSearch, 'partials.search.search-modal')
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
