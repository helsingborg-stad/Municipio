@element([
    'classList' => $classList
])
    @element([
        'classList' => array_merge([
            'mod-menu__wrapper',
            'o-layout-grid',
            'o-layout-grid--cq'
        ], $wrapperClasses)
    ])
        @foreach ($menu['items'] as $index => $menuItem)
            @include('menus.listing.components.menuItem')
        @endforeach
    @endelement
@endelement