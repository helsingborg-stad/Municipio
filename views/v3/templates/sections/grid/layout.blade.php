@element([
    'classList' => $classes ?? [
        'o-container',
        'o-layout-grid',
        'o-layout-grid--cols-1',
        'u-margin__y--4',
        'o-layout-grid--row-gap-12',
    ]
])
    @yield('layout')
@endelement