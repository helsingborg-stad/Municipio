@include('partials.chat', [
    'header' => false,
    'chatConfig' => [
        'classList' => ['c-chat--flat'],
        'attributeList' => array_merge($attributeList, ['municipio-chat-block' => '1']),
    ],
])