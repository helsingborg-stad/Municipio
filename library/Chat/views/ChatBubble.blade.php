@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'text' => 'Chatta',
        'reversePositions' => true
    ]
])
    <div class="u-display--flex u-flex-direction--column u-gap-2" style="max-height: 60vh;">
        @typography(['variant' => 'h6', 'classList' => ['c-fab__heading']])
            Chatta med oss
        @endtypography

        <div data-chat-messages="" class="chat-messages u-padding--2" style="overflow-y: auto; overflow-wrap: anywhere;">
            {{-- Chat messages will be appended here --}}
            <template data-chat-template-user="">
                @comment([
                    'author' => 'Du',
                    'text' => 'asdf',
                    'is_reply' => false,
                    'date' => ''
                ])
                @endcomment
            </template>

            <template data-chat-template-assistant="">
                @comment([
                    'author' => 'Assistent',
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
            'label' => 'Skriv din fråga här',
            'multiline' => true
        ])
        @endfield
        @button([
            'text' => 'Skicka',
            'color' => 'primary',
            'style' => 'filled',
        ])
        @endbutton
        @endform
    </div>
@endfab
