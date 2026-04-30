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
    'size' => 'xl'
])
    @chat([
        'id' => 'global-chat',
        'persistent' => true,
        'title' => $lang['chat'],
        'clearButton' => [
            'text' => $lang['clear'],
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
                'text' => $lang['clear'],
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
