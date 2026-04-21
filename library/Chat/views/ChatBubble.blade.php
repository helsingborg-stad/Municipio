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
    <div class="u-display--flex u-flex-direction--column u-gap-2" style="max-height: 60vh;">
        @typography([
            'element' => 'h6',
            'variant' => 'h6',
            'classList' => ['c-fab__heading']
        ])
            {{ $i18n['chatWithAi'] }}
        @endtypography

        <div data-js-chat-messages="1" class="chat-messages u-padding--2" style="overflow-y: auto; overflow-wrap: anywhere;">
            {{-- Chat messages will be appended here --}}
            <template data-js-chat-template-user="1">
                @comment([
                    'author' => $i18n['you'],
                    'text' => 'asdf',
                    'is_reply' => false,
                    'date' => ''
                ])
                @endcomment
            </template>

            <template data-js-chat-template-assistant="1">
                @comment([
                    'author' => $i18n['assistant'],
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
        'attributeList' => ['data-js-chat-form' => true]
        ])
        @field([
            'type' => 'text',
            'label' => $i18n['writeQuestion'],
            'multiline' => true
        ])
        @endfield
        @button([
            'text' => $i18n['send'],
            'color' => 'primary',
            'style' => 'filled',
        ])
        @endbutton
        @endform
    </div>
@endfab
