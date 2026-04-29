@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'reversePositions' => true
    ]
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
            {{-- @element([
                'classList' => ['u-position--absolute'],
                'attributeList' => [
                    'style' => 'top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%;'
                ]
            ])
                hej
            @endelement --}}
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
