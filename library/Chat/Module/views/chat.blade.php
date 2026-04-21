@php
    $rootAttributes = ['data-js-chat-module' => true];
    if (!empty($assistant_id)) {
        $rootAttributes['data-js-chat-assistant'] = $assistant_id;
    }
@endphp
@group([
    'direction' => 'vertical',
    'classList' => ['u-margin__bottom--2', 'u-margin__top--2', 'u-gap-1'],
    'attributeList' => $rootAttributes
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
        @if (!empty($subtitle))
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
        'attributeList' => ['data-js-chat-form' => true]
        ])
        @group([
            'direction' => 'horizontal',
            'alignItems' => 'end',
            'classList' => ['u-gap-2', 'u-position--relative', 'u-width--100'],
            'attributeList' => ['data-js-chat-initial-group' => true]
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
                'text' => !empty($button_label) ? $button_label : $i18n['submit'],
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
                'data-js-chat-main-group' => true
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
                'attributeList' => ['data-js-chat-close-button' => true],
            ])
            @endbutton
            <div data-js-chat-messages="1" style="display: flex; flex-direction: column; overflow-y: auto; max-height: 60vh;">
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
            'classList' => ['u-display--flex', 'u-flex-direction--row', 'u-gap-2'],
            'attributeList' => ['data-js-chat-form' => true]
            ])
            @field([
                'type' => 'text',
                'placeholder' => $i18n['writeQuestion']
            ])
            @endfield
            @button([
                'text' => $i18n['send'],
                'color' => 'primary',
                'style' => 'filled',
                'attributeList' => ['data-js-chat-send-button' => true],
            ])
            @endbutton
            @endform
        @endgroup
    @endgroup
@endgroup
