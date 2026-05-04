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
                    'data-js-chat-clear' => ''
                ],
                'classList' => ['u-margin__left--auto']
            ])
            @endbutton
        @endslot
    @endchat
@endfab
