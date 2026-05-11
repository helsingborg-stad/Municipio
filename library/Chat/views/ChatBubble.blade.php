@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'reversePositions' => true
    ],
    'size' => 'xl',
    'attributeList' => [
        'style' => '--c-fab--panel-padding: 0px;'
    ]
])
    @chat([
        'id' => 'global-chat',
        'persistent' => true,
        'height' => 'min(80vh, calc(var(--base, 8px) * 75))',
        'attributeList' => [
            'style' => '--c-chat--inner-padding-multiplier: 2;',
        ],
        'chatInputData' => [
            'sendButtonText' => $lang['send'],
            'placeholderText' => $lang['placeholder']
        ]
    ])
        @slot('titleArea')
            @element([
                'classList' => ['o-layout-grid', 'o-layout-grid--cols-2', 'o-layout-grid--gap-2']
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
                @typography([
                    'element' => 'h2',
                    'variant' => 'h6',
                    'classList' => ['o-layout-grid--align-center'],
                ])
                    {{ $lang['chat'] }}
                @endtypography
            @endelement
            @element([
                'classList' => ['u-margin__left--auto', 'o-layout-grid--align-center']
            ])
                @button([
                    'icon' => 'new_window',
                    'size' => 'sm',
                    'style' => 'filled',
                    'color' => 'default',
                    'reversePositions' => true,
                    'attributeList' => [
                        'data-js-chat-new' => true,
                    ],
                ])
                @endbutton
                @button([
                    'icon' => 'keyboard_double_arrow_down',
                    'size' => 'sm',
                    'style' => 'filled',
                    'color' => 'default',
                    'reversePositions' => true,
                    'attributeList' => [
                        'data-js-chat-clear' => true,
                        'data-js-toggle-trigger' => 'chat-global-root'
                    ],
                ])
                @endbutton
            @endelement
        @endslot
    @endchat
@endfab
