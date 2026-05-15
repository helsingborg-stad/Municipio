@fab([
    'id' => 'chat-global-root',
    'position' => 'bottom-right',
    'heading' => '',
    'button' => [
        'icon' => 'chat',
        'size' => 'md',
        'color' => 'primary',
        'reversePositions' => true
    ],
    'size' => 'xl',
    'attributeList' => [
        'style' => '--c-fab--panel-padding: 0px;'
    ]
])
    @chat([
        'id' => 'global-chat',
        'persistent' => true,
        'size' => 'sm',
        'attributeList' => $attributeList,
        'chatInputData' => [
            'sendButtonText' => $lang['send'],
            'placeholderText' => $lang['placeholder']
        ]
    ])
        @slot('titleArea')
            @element([
                'classList' => ['o-layout-grid', 'o-layout-grid--cols-2', 'o-layout-grid--gap-2']
            ])
                @avatar([
                    'image' => $avatar['src'] ?? null,
                    'classList' => ['o-layout-grid--align-center'],
                    'size' => 'sm',
                    'icon' => [
                        'name' => 'person',
                        'size' => 'md'
                    ]
                ])
                @endavatar
                @if (!empty($name))
                    @typography([
                        'element' => 'h2',
                        'variant' => 'h6',
                        'classList' => ['o-layout-grid--align-center'],
                    ])
                        {{ $name }}
                    @endtypography
                @endif
            @endelement
            @element([
                'classList' => ['u-margin__left--auto', 'o-layout-grid--align-center']
            ])
                @button([
                    'icon' => 'edit_square',
                    'size' => 'md',
                    'style' => 'basic',
                    'color' => 'default',
                    'reversePositions' => true,
                    'attributeList' => [
                        'data-js-chat-new' => true,
                        'aria-label' => $lang['newConversation'],
                        'data-tooltip' => $lang['newConversation']
                    ],
                ])
                @endbutton
                @button([
                    'icon' => 'close',
                    'size' => 'md',
                    'style' => 'basic',
                    'color' => 'default',
                    'reversePositions' => true,
                    'classList' => [
                        'u-margin__left--0'
                    ],
                    'attributeList' => [
                        'data-js-chat-clear' => true,
                        'data-js-toggle-trigger' => 'chat-global-root',
                        'aria-label' => $lang['close'],
                        'data-tooltip' => $lang['close']
                    ],
                ])
                @endbutton
            @endelement
        @endslot
    @endchat
@endfab
