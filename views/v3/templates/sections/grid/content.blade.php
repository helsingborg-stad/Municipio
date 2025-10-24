@element([
    'id' => 'main-content',
    'componentElement' => 'main',
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cols-12',
        'o-layout-grid--column-gap-8',
        'o-layout-grid--row-gap-12',
    ]
])
    @include('templates.sections.grid.left-sidebar')
    @include('templates.sections.grid.right-sidebar')
    @section('main-content')
        @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => []])
        @yield('content')
        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => []])
    @stop

    @php
        $leftSidebarHasContent = !empty(trim($__env->yieldContent('sidebar-left')));
        $rightSidebarHasContent = !empty(trim($__env->yieldContent('sidebar-right')));
        $mainColumnSize = 12;

        if ($leftSidebarHasContent && $rightSidebarHasContent) {
            $mainColumnSize = 6;
        } elseif ($leftSidebarHasContent || $rightSidebarHasContent) {
            $mainColumnSize = 8;
        }
    @endphp
    @yield('before-content')
    @hasSection('sidebar-left')
        @element([
            'componentElement' => 'aside',
            'classList' => [
                'o-layout-grid',
                'o-layout-grid--col-span-' . ($rightSidebarHasContent ? 3 : 4) . '@md',
                'o-layout-grid--col-span-12',
                'o-layout-grid--gap-6',
                'u-print-display--none',
                'o-layout-grid--grid-auto-rows-min-content',
                'o-layout-grid--order-1@md',
                'o-layout-grid--order-2'
            ]
        ])
            @yield('sidebar-left')
        @endelement
    @endif
    @hasSection('main-content')
        @element([
            'componentElement' => 'article',
            'classList' => [
                'o-layout-grid--col-span-' . $mainColumnSize . '@md',
                'o-layout-grid--col-span-12',
                'o-layout-grid',
                'o-layout-grid--gap-6',
                'o-layout-grid--grid-auto-rows-min-content',
                'o-layout-grid--order-2@md',
                'o-layout-grid--order-1'
            ]
        ])
            @yield('main-content')
        @endelement
    @endif
    @hasSection('sidebar-right')
        @element([
            'componentElement' => 'aside',
            'classList' => [
                'o-layout-grid',
                'o-layout-grid--gap-6',
                'o-layout-grid--col-span-' . ($leftSidebarHasContent ? 3 : 4) . '@md',
                'o-layout-grid--col-span-12',
                'u-print-display--none',
                'o-layout-grid--grid-auto-rows-min-content',
                'o-layout-grid--order-3',
            ]
        ])
            @yield('sidebar-right')
        @endelement
    @endif
    @yield('below-content')
@endelement