@group([
    'direction' => 'vertical',
    'classList' => ['u-margin__bottom--2', 'u-margin__top--2', 'u-gap-1'],
    'attributeList' => [
        'data-chat-assistant' => $assistant_id ?? ''
    ]
])
    @group([
        'direction' => 'vertical'
    ])
        @if (!empty($title))
            @typography([
                'variant' => 'h1'
            ])
                {{ $title }}
            @endtypography
        @endif
        @if (!empty($title))
            @typography([
                'variant' => 'p'
            ])
                {{ $subtitle }}
            @endtypography
        @endif
    @endgroup

    @group([
        'classList' => ['u-position--relative']
    ])
        @form([
        'action' => '#',
        'method' => 'POST',
        'classList' => ['u-width--100'],
        'attributeList' => ['data-chat-form' => '']
        ])
        @group([
            'direction' => 'horizontal',
            'alignItems' => 'end',
            'classList' => ['u-gap-2', 'u-position--relative', 'u-width--100'],
            'attributeList' => ['data-chat-initial-group' => '']
        ])
            @field([
                'type' => 'search',
                'name' => 'search',
                'placeholder' => $placeholder ?? '',
                'required' => true,
                'icon' => ['icon' => 'search']
            ])
            @endfield
            @button([
                'color' => 'primary',
                'size' => 'md',
                'text' => $button_label ?? __('Submit', 'municipio'),
            ])
            @endbutton
        @endgroup
        @endform



        <!-- Floating conversation window -->
        @group([
            'direction' => 'vertical',
            'classList' => [
                'u-gap-2',
                'u-color__bg--default',
                'u-position--absolute',
                'u-top--0',
                'u-left--1',
                'u-padding--2',
                'u-level-top',
                'u-width--50'
            ],
            'attributeList' => [
                'data-chat-main-group' => ''
            ]
        ])
            @button([
                'icon' => 'close',
                'size' => 'sm',
                'color' => 'transparent',
                'classList' => [
                    'u-position--absolute',
                    'u-top--1',
                    'u-right--1',
                    'u-level-top',
                ],
                'attributeList' => ['data-chat-close-button' => ''],
            ])
            @endbutton
            <div data-chat-messages="" style="display: flex; flex-direction: column; overflow-y: auto; max-height: 60vh;">
                <template data-chat-template-user="y">
                    @comment([
                        'author' => __('You', 'municipio'),
                        'text' => 'asdf',
                        'is_reply' => false,
                        'date' => ''
                    ])
                    @endcomment
                </template>

                <template data-chat-template-assistant="y">
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
            'classList' => ['u-display--flex', 'u-flex-direction--row', 'u-gap-2'],
            'attributeList' => ['data-chat-form' => '']
            ])
            @field([
                'type' => 'text',
                'placeholder' => __('Write your question here', 'municipio')
            ])
            @endfield
            @button([
                'text' => __('Send', 'municipio'),
                'color' => 'primary',
                'style' => 'filled',
                'attributeList' => ['data-chat-send-button' => ''],
            ])
            @endbutton
            @endform
        @endgroup
    @endgroup
@endgroup
