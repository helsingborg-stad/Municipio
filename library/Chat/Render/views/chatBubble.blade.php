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
        @chat([
            'id' => $chatId,
            'persistent' => true,
            'size' => 'xs',
            'attributeList' => array_merge(['data-js-municipio-chat-bubble' => '1', 'data-js-municipio-chat-mode' => 'ai'], $attributeList),
            'classList' => ['municipio-chat__chat', 'u-visibility--hidden'],
            'chatInputData' => [
                'sendButtonText' => $lang['send'],
                'placeholderText' => $lang['placeholder']
            ]
        ])
            @slot('titleArea')
            @element([
                'componentElement' => 'template',
                'attributeList' => [
                    'data-js-municipio-chat-actions' => 'true'
                ]
            ])
                @button([
                    'icon' => 'edit_square',
                    'size' => 'md',
                    'style' => 'basic',
                    'color' => 'default',
                    'reversePositions' => true,
                    'attributeList' => [
                        'data-js-chat-new-session' => true,
                        'aria-label' => $lang['newConversation'],
                        'data-tooltip' => $lang['newConversation']
                    ],
                ])
                @endbutton
                @button([
                    'icon' => 'minimize',
                    'size' => 'md',
                    'style' => 'basic',
                    'color' => 'default',
                    'reversePositions' => true,
                    'classList' => [
                        'u-margin__left--0'
                    ],
                    'attributeList' => [
                        'data-js-chat-minimize' => true,
                        'popovertarget' => 'chat-global-root',
                        'popovertargetaction' => 'hide',
                        'aria-label' => $lang['close'],
                        'data-tooltip' => $lang['close']
                    ],
                ])
                @endbutton
            @endelement
                @element([
                    'classList' => ['u-display--flex', 'o-layout-grid--gap-2'],
                ])
                    @avatar([
                        'image' => $avatar['src'] ?? null,
                        'classList' => ['o-layout-grid--align-center'],
                        'size' => 'sm',
                        'icon' => [
                            'name' => 'person',
                            'size' => 'md'
                        ]
                    ])
                    @endavatar
                    @if (!empty($name))
                        @typography([
                            'element' => 'h2',
                            'variant' => 'h6',
                            'classList' => ['o-layout-grid--align-center'],
                        ])
                            {{ $name }}
                        @endtypography
                    @endif
                @endelement
                @element([
                    'attributeList' => [
                        'data-js-chat-actions' => true,
                    ],
                    'classList' => ['u-margin__left--auto', 'o-layout-grid--align-center', 'u-display--flex']
                ])
                <!-- Chat actions -->
                @endelement
            @endslot
            @include('partials.feedback')
        @endchat
    @endelement
@endpopover