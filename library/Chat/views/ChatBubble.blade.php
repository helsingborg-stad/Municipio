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
        'title' => $lang['chat'],
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
