{{-- 
    This is the main layout file for grid content. It wraps everything in a container class which means it won't be full width at every screen size. Everything used in here will be be the full width of the article container.
--}}
@element([
    'classList' => $classes ?? [
        'o-container',
        'o-layout-grid',
        'o-layout-grid--cols-1',
        'u-margin__y--4',
        'o-layout-grid--row-gap-12',
    ]
])
    @section('before-content-notice-area')
        @include('templates.sections.content-notices', ['classes' => []])
    @show

    @section('above-content')
        @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => $aboveSidebarClasses ?? []])
    @show

    @yield('layout')

    @section('below-content')
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => $belowSidebarClasses ?? []])
    @show
@endelement