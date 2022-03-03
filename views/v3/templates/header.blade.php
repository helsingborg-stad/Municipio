@header([
    'id' => 'site-header',
    'classList' => [
        'site-header', isset($classList) ? is_array($classList) ? implode(' ', $classList) : $classList : ''
    ],
    'attributeList' => [
        'js-toggle-item' => 'hamburger-menu',
        'js-toggle-class' => 'hamburger-menu-open'
    ],
    'context' => 'site.header'
])

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

@includeIf('partials.hero')
@includeIf('partials.sidebar', ['id' => 'top-sidebar'])
