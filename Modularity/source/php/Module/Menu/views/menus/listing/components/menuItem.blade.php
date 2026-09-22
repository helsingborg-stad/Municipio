@element([
    'classList' => [
        'mod-menu__item',
    ],
    'attributeList' => [
        'data-js-toggle-item' => 'mod-menu-item-' . $menuItem['id'] . '-' . $index . '-' . $menuIndex,
        'data-js-toggle-class' => "is-expanded"
    ]
])
        @element([
            'classList' => [
                'mod-menu__item-content',
                'o-layout-grid',
                'o-layout-grid--cols-1',
                'o-layout-grid--gap-2'
            ],
        ])
            @include('menus.listing.partials.heading')
            @include('menus.listing.partials.description')
            @include('menus.listing.partials.children')
        @endelement
        @include('menus.listing.partials.expand')
@endelement