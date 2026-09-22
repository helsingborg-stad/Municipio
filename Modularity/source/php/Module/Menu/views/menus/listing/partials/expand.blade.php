@element([
    'classList' => [
        'mod-menu__expand',
    ],
    'attributeList' => [
        'role' => 'button',
        'data-js-toggle-trigger' => 'mod-menu-item-' . $menuItem['id'] . '-' . $index
    ]
])
    @element([
        'classList' => [
            'mod-menu__expand-content',
        ]
    ])
        Visa alla ({{ count($menuItem['children']) }})
    @endelement
    @icon([
        'icon' => 'keyboard_arrow_down',
        'classList' => [
            'mod-menu__expand-icon',
        ]
    ])
    @endicon
@endelement