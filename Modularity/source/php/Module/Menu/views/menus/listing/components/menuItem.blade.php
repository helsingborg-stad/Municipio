@element([
    'classList' => [
        'mod-menu__item',
    ],
    'attributeList' => [
        'data-js-toggle-item' => "mod-menu-item-{{$ID}}-{{$index}}",
        'data-js-toggle-class' => "is-expanded"
    ]
])
        @element([
            'classList' => [
                'o-layout-grid',
                'o-layout-grid--cols-1'
            ],
        ])
            @include('menus.listing.partials.heading')
            @include('menus.listing.partials.description')
            @include('menus.listing.partials.children')
        @endelement
@endelement