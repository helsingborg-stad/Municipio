@button([
    'style' => 'basic',
    'pressed' => false,
    'classList' => [
        'mod-menu__children-toggle',
    ],
    'attributeList' => [
        'data-js-toggle-trigger' => 'mod-menu-item-' . $ID . '-' . $index,
    ]
])
    @icon([
        'icon' => 'keyboard_arrow_down',
        'size' => 'md',
        'classList' => [
            'mod-menu__children-toggle-icon',
        ]
    ])
    @endicon
@endbutton