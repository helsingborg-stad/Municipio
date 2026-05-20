@slot('belowChatArea')
    @element([
        'componentElement' => 'template',
        'attributeList' => [
            'data-js-chat-feedback' => true
        ]
    ])
        @element([
            'classList' => [
                'municipio-ai-chat__feedback'
            ]
        ])
            @icon([
                'icon' => 'thumb_up',
                'size' => 'sm',
                'attributeList' => [
                    'role' => 'button',
                    'data-js-chat-message-like-button' => true
                ],
            ])
            @endicon
            @icon([
                'icon' => 'thumb_down',
                'size' => 'sm',
                'attributeList' => [
                    'role' => 'button',
                    'data-js-chat-message-dislike-button' => true
                ],
            ])
            @endicon
            <!-- Feedback area -->
        @endelement
    @endelement
@endslot