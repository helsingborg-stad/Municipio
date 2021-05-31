@header([
    'id' => 'site-header',
    'classList' => [
        'site-header', isset($classList) ? is_array($classList) ? implode(' ', $classList) : $classList : ''
    ],
    'context' => 'siteHeader'
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
    
    {{-- Mobile Navigation --}}
    @section('mobile-navigation')
        @sidebar([
            'logo' => $logotype->url,
            'items' => $mobileMenuItems,
            'pageId' => $pageID,
            'classList' => [
                'l-docs--sidebar',
                'c-sidebar--fixed',
                'u-visibility--hidden@md',
                'u-visibility--hidden@lg',
                'u-visibility--hidden@xl'
            ],
            'attributeList' => [
                'js-toggle-item' => 'js-mobile-sidebar',
                'js-toggle-class' => 'c-sidebar--collapsed'
            ],
            'endpoints' => [
                'children' => $homeUrlPath . '/wp-json/municipio/v1/navigation/children'
            ],
        ])
        @endsidebar
    @show
    
    @section('helper-navigation')
        @includeIf('partials.navigation.helper')
    @show

@endheader

@includeIf('partials.hero')
@includeIf('partials.sidebar', ['id' => 'top-sidebar'])
