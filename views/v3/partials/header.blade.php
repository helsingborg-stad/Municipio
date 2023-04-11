@header(['id' => 'site-header'])

    {{-- NAVIGATION PRIMARY NAV --}}
    @includeIf('partials.navigation.primary')

    {{-- After header body --}}
    @yield('after-header-body')

@endheader

@includeIf('partials.hero')

@includeIf('partials.sidebar', ['id' => 'top-sidebar'])