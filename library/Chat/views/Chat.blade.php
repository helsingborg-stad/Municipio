@fab([
    'id' => 'chat-root',
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
    <div id="chat-panel" class="u-display--flex u-flex-direction--column u-gap-2" style="max-height: 60vh;">
        @typography(['variant' => 'h6', 'classList' => ['c-fab__heading']])
            Chatta med oss
        @endtypography

        <div id="chat-messages" class="u-padding--2" style="overflow-y: auto; overflow-wrap: anywhere;">
            {{-- Chat messages will be appended here --}}
        </div>

        @form([
        'id' => 'chat-form',
        'action' => '#',
        'method' => 'POST',
        'classList' => ['u-display--flex', 'u-flex-direction--column', 'u-gap-2']
        ])
        @field([
            'id' => 'chat-input',
            'type' => 'text',
            'name' => 'text',
            'label' => 'Skriv din fråga här',
            'multiline' => true
        ])
        @endfield
        @button([
            'id' => 'chat-submit',
            'text' => 'Skicka',
            'color' => 'primary',
            'style' => 'filled',
        ])
        @endbutton
        @endform
    </div>
@endfab

<template id="chat-message-template-user">
    @comment([
        'author' => 'Du',
        'text' => 'asdf',
        'is_reply' => true,
        'date' => ''
    ])
    @endcomment
</template>

<template id="chat-message-template-assistant">
    @comment([
        'author' => 'Assistent',
        'text' => 'asdf',
        'is_reply' => false,
        'date' => ''
    ])
    @endcomment
</template>
