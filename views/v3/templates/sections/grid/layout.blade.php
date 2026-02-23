{{-- 
    This is the main layout file for grid content. It wraps everything in a container class which means it won't be full width at every screen size.
    Use the layout "yield" to add content. It's meant to wrap "content.blade.php", however, it's not included here due to the fact that we want to be able to use this layout file for other purposes as well or create other "content" files.
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

    @section('above')
        @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => $aboveSidebarClasses ?? []])
    @show

    @yield('layout')

    @section('below')
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => $belowSidebarClasses ?? []])
    @show
@endelement