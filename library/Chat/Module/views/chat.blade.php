@php
    $rootAttributes = ['data-js-chat-module' => true];
    if (!empty($assistant_id)) {
        $rootAttributes['data-js-chat-assistant'] = $assistant_id;
    }
@endphp
@chat([
    'id' => $id,
    'chatInputData' => [
        'sendButtonText' => $lang['send'],
        'placeholderText' => $lang['placeholder']
    ],
    'classList' => [
        'c-chat--flat'
    ]
])
@endchat