@chat([
    'id' => $chatId,
    'persistent' => false,
    'classList' => ['c-chat--flat'],
    'attributeList' => $attributeList,
    'chatInputData' => [
        'sendButtonText' => $lang['send'],
        'placeholderText' => $lang['placeholder']
    ]
])
    @include('partials.feedback')
@endchat