@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'text' => $lang['chat'],
        'reversePositions' => true
    ]
])
    @chat([
        'id' => 'global-chat',
        'persistent' => true,
        'chatInputData' => [
            'sendButtonText' => $lang['send'],
            'placeholderText' => "PLACEHOLDER TEXT"
        ]
    ])
    @endchat
@endfab
