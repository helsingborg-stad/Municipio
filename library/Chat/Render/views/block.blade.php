@chat([
    'persistent' => false,
    'classList' => ['c-chat--flat'],
    'attributeList' => array_merge($attributeList, ['municipio-ai-chat-block']),
    'chatInputData' => [
        'sendButtonText' => $lang['send'],
        'placeholderText' => $lang['placeholder']
    ]
])
    @include('partials.feedback')
@endchat