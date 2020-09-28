<header class="site-header {{isset($classnames) ? is_array($classnames) ? implode(' ', $classnames) : $classnames : ''}}" id="site-header">
    {{-- Search Form --}}
    @section('search-form')
        {{-- <h2>Search form</h2> --}}
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
        @includeIf('partials.navigation.modal')
    @show
</header>