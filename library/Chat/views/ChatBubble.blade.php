@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'text' => __('Chat', 'municipio'),
        'reversePositions' => true
    ]
])
    <div class="u-display--flex u-flex-direction--column u-gap-2" style="max-height: 60vh;">
        @typography(['variant' => 'h6', 'classList' => ['c-fab__heading']])
            {{ __('Chat with AI', 'municipio') }}
        @endtypography

        <div data-chat-messages="" class="chat-messages u-padding--2" style="overflow-y: auto; overflow-wrap: anywhere;">
            {{-- Chat messages will be appended here --}}
            <template data-chat-template-user="">
                @comment([
                    'author' => __('You', 'municipio'),
                    'text' => 'asdf',
                    'is_reply' => false,
                    'date' => ''
                ])
                @endcomment
            </template>

            <template data-chat-template-assistant="">
                @comment([
                    'author' => __('Assistant', 'municipio'),
                    'text' => 'asdf',
                    'is_reply' => false,
                    'date' => ''
                ])
                @endcomment
            </template>
        </div>

        @form([
        'action' => '#',
        'method' => 'POST',
        'classList' => ['u-display--flex', 'u-flex-direction--column', 'u-gap-2'],
        'attributeList' => ['data-chat-form' => '']
        ])
        @field([
            'type' => 'text',
            'label' => __('Write your question here', 'municipio'),
            'multiline' => true
        ])
        @endfield
        @button([
            'text' => __('Send', 'municipio'),
            'color' => 'primary',
            'style' => 'filled',
        ])
        @endbutton
        @endform
    </div>
@endfab
