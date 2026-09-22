@if(count($menuItem['children']) > 3)
    @element([
        'classList' => [
            'mod-menu__expand',
        ],
        'attributeList' => [
            'role' => 'button',
            'data-js-toggle-trigger' => 'mod-menu-item-' . $menuItem['id'] . '-' . $index . '-' . $menuIndex
        ]
    ])
        @element([
            'classList' => [
                'mod-menu__expand-content',
            ]
        ])
            @element([
                'componentElement' => 'span',
                'classList' => [
                    'mod-menu__expand-text',
                ]
            ])
                {{ $lang['showAll'] }} ({{ (count($menuItem['children']) - 3) }})
            @endelement
            @element([
                'componentElement' => 'span',
                'classList' => [
                    'mod-menu__expand-text',
                    'mod-menu__expand-text-hidden'
                ]
            ])
                {{ $lang['hide'] }}
            @endelement
        @endelement
        @icon([
            'icon' => 'keyboard_arrow_down',
            'classList' => [
                'mod-menu__expand-icon',
            ]
        ])
        @endicon
    @endelement
@endif