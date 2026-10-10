@slot('belowChatArea')
    @element([
        'componentElement' => 'template',
        'attributeList' => [
            'data-js-chat-feedback' => true
        ]
    ])
        @element([
            'classList' => [
                'municipio-chat__feedback',
                'municipio-ai-chat__feedback'
            ]
        ])
            @icon([
                'icon' => 'thumb_up',
                'size' => 'sm',
                'classList' => [
                    'municipio-chat__feedback-like-button',
                    'municipio-ai-chat__feedback-like-button'
                ],
                'attributeList' => [
                    'role' => 'button',
                    'data-js-chat-message-like-button' => true,
                    'data-tooltip' => $lang['like']
                ],
            ])
            @endicon
            @icon([
                'icon' => 'thumb_down',
                'size' => 'sm',
                'classList' => [
                    'municipio-chat__feedback-dislike-button',
                    'municipio-ai-chat__feedback-dislike-button'
                ],
                'attributeList' => [
                    'role' => 'button',
                    'data-js-chat-message-dislike-button' => true,
                    'data-tooltip' => $lang['dislike']
                ],
            ])
            @endicon
            <!-- Feedback area -->
        @endelement
    @endelement
@endslot