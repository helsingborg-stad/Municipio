@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'text' => $i18n['chat'],
        'reversePositions' => true
    ]
])
    @chat([
        'id' => 'global-chat'
    ])
    @endchat
@endfab
