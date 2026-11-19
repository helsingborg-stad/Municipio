@element([
    'componentElement' => 'template',
    'attributeList' => [
        'data-js-municipio-chat-template' => 'true'
    ]
])
    @button([
        'icon' => 'edit_square',
        'size' => 'md',
        'style' => 'basic',
        'color' => 'default',
        'reversePositions' => true,
        'attributeList' => [
            'data-js-chat-clear' => true,
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
            'aria-label' => $lang['minimize'],
            'data-tooltip' => $lang['minimize']
        ],
    ])
    @endbutton
    @button([
        'icon' => 'close',
        'size' => 'md',
        'style' => 'basic',
        'color' => 'default',
        'reversePositions' => true,
        'classList' => [
            'u-margin__left--0'
        ],
        'attributeList' => [
            'data-js-chat-close' => true,
            'popovertarget' => 'chat-global-root',
            'popovertargetaction' => 'hide',
            'aria-label' => $lang['close'],
            'data-tooltip' => $lang['close']
        ],
    ])
    @endbutton
    @element([
        'classList' => [
            'u-position--absolute',
            'u-level--1',
            'u-height--100',
            'u-width--100',
            'u-display--flex',
            'u-align-items--center',
            'u-justify-content--center'
        ],
        'attributeList' => [
            'data-js-chat-close-confirmation' => true,
            'style' => 'background-color: var(--color--surface-alt, #fff)'
        ]
    ])
        @button([
            'icon' => 'arrow_back',
            'text' => $lang['cancel'],
            'color' => 'primary',
            'style' => 'outlined',
            'reversePositions' => true,
            'attributeList' => [
                'data-js-chat-close-cancel' => true
            ]
        ])
        @endbutton
        @button([
            'icon' => 'check',
            'text' => $lang['closeChat'],
            'color' => 'primary',
            'reversePositions' => true,
            'attributeList' => [
                'data-js-chat-close-confirm' => true
            ]
        ])
        @endbutton
    @endelement
    @avatar([
        'classList' => ['o-layout-grid--align-center'],
        'attributeList' => [
            'data-js-chat-avatar' => 0
        ],
        'size' => 'sm',
        'icon' => [
            'name' => 'person',
            'size' => 'md'
        ]
    ])
    @endavatar
    @avatar([
        'classList' => ['o-layout-grid--align-center'],
        'attributeList' => [
            'data-js-chat-avatar' => 1
        ],
        'size' => 'sm',
        'image' => '#'
    ])
    @endavatar
@endelement
    @element([
        'attributeList' => [
            'data-js-chat-heading-area' => true
        ],
        'classList' => ['u-display--flex', 'o-layout-grid--gap-2'],
    ])
        @typography([
            'element' => 'h2',
            'variant' => 'h6',
            'attributeList' => [
                'data-js-chat-title' => true,
            ],
            'classList' => ['o-layout-grid--align-center'],
        ])
            <!-- insert name -->
        @endtypography
    @endelement
    @element([
        'attributeList' => [
            'data-js-chat-actions' => true,
        ],
        'classList' => ['u-margin__left--auto', 'o-layout-grid--align-center', 'u-display--flex']
    ])
    <!-- Chat actions -->
    @endelement