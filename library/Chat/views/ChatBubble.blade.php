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
            @button([
                'icon' => 'close',
                'size' => 'md',
                'style' => 'basic',
                'text' => $lang['close'],
                'reversePositions' => true,
                'attributeList' => [
                    'data-js-chat-clear' => true,
                    'style' => 'margin-top: calc(var(--base, 8px) * -2);',
                    'data-js-toggle-trigger' => 'chat-global-root'
                ],
                'classList' => ['u-margin__left--auto']
            ])
            @endbutton
        @endslot
    @endchat
@endfab
