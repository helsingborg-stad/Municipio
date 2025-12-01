@section('sidebar-left')
    @include('partials.sidebar', [
        'id' => 'left-sidebar',
        'classes' => [
            'o-layout-grid',
            'o-layout-grid--gap-6'
        ],
    ])

    @yield('sidebar-left-content')

    @include('partials.sidebar', [
        'id' => 'left-sidebar-bottom',
        'classes' => [
            'o-layout-grid',
            'o-layout-grid--gap-6'
        ],
    ])
@stop