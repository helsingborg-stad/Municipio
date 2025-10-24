@element([
    'classList' => $classes ?? [
        'o-container',
        'o-layout-grid',
        'o-layout-grid--cols-1',
        'u-margin__y--8',
        'o-layout-grid--row-gap-8',
    ]
])
    @yield('layout')
@endelement