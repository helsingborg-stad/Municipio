@chat([
    'classList' => ['municipio-ai-chat', 'c-chat--flat'],
    'attributeList' => $attributeList,
    'chatInputData' => [
        'sendButtonText' => $lang['send'],
        'placeholderText' => $lang['placeholder']
    ]
])
    @include('partials.feedback')
@endchat