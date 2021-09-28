@header([
	'id' => 'site-header',
	'attributeList' => ['style' => 'overflow-x: hidden;']
])

    {{-- NAVIGATION PRIMARY NAV --}}
    @includeIf('partials.navigation.primary')

    {{-- After header body --}}
    @yield('after-header-body')

@endheader

@includeIf('partials.navigation.drawer')

@includeIf('partials.hero')

@includeIf('partials.sidebar', ['id' => 'top-sidebar'])