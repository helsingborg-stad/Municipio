@element([
    'classList' => [
        'kulturkortet-actions',
    ],
    'attributeList' => [
        'data-js-toggle-item' => 'kulturkortet-actions-menu',
        'data-js-toggle-class' => 'is-expanded',
        'data-js-click-away' => 'is-expanded'
    ]
])
    @button([
        'color' => 'default',
        'style' => 'filled',
        'icon' => 'more_vert',
        'toggle' => true,
        'classList' => [
            'kulturkortet-actions__menu-button',
        ],
        'attributeList' => [
            'data-js-toggle-trigger' => 'kulturkortet-actions-menu',
            'data-js-click-away-remove-pressed' => ''
        ]
    ])
    @endbutton
    @card([
        'classList' => [
            'kulturkortet-actions__menu-card'
        ],
    ])
    @slot('aboveContent')
        @nav([
            'items' => $actions,
            'direction' => 'vertical',
            'includeToggle' => false,
            'height' => 'md',
        ])
        @endnav
    @endslot
    @endcard
@endelement