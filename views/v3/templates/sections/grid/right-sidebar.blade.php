@section('sidebar-right')
    @include('partials.sidebar', [
        'id' => 'right-sidebar',
        'classes' => [
            'o-layout-grid',
            'o-layout-grid--gap-6'
        ],
    ])

    @yield('sidebar-right-content')

    @include('partials.sidebar', [
        'id' => 'right-sidebar-bottom',
        'classes' => [
            'o-layout-grid',
            'o-layout-grid--gap-6'
        ],
    ])
@stop