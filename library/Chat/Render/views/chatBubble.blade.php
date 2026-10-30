@button([
    'icon' => 'chat',
    'size' => 'md',
    'color' => 'primary',
    'reversePositions' => true,
    'classList' => [
        'u-position--fixed',
        'u-right--2',
        'u-bottom--2'
    ],
    'attributeList' => [
        'popovertarget' => 'chat-global-root',
        'style' => 'bottom: 1rem; right: 1rem;',
        'data-js-municipio-chat-bubble-trigger' => '1'
    ]
])
@endbutton
@popover([
    'id' => 'chat-global-root',
    'horizontalPlacement' => 'right',
    'verticalPlacement' => 'top',
    'relative' => true,
    'classList' => [
        'u-margin__bottom--1'
    ]
])
    @element([
        'attributeList' => [
            'data-js-municipio-chat-wrapper' => 'true',
        ],
        'classList' => [
            'municipio-chat'
        ]
    ])
        @include('partials.chat', [
            'actions' => true,
            'chatConfig' => [
                'id' => $chatId,
                'size' => 'xs',
                'attributeList' => array_merge(['data-js-municipio-chat-bubble' => '1'], $attributeList),
                'classList' => ['municipio-chat__chat', 'u-visibility--hidden'],
            ],
            'header' => true,
            'lang' => $lang,
            'avatar' => $avatar,
            'name' => $name,
        ])
    @endelement
@endpopover