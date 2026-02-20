@element([
    'classList' => $classList ?? array_merge(
        $addToDefaultClassList ?? [], [
            'o-container',
            'o-layout-grid',
            'o-layout-grid--cols-1',
            'o-layout-grid--row-gap-12',
        ]
    )
])
    @yield('layout')
@endelement