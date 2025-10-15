@element([
    'attributeList' => [                
        'data-js-user-editable-user' => $user,
        'data-js-user-editable-id' => $privateModuleMetaKey,
        'data-js-user-editable' => $userMetaKey
    ],
])
    @includeFirst([$template, 'base'],
    [
        'manualInputs' => $filteredManualInputs,
        'titleIcon' => [
            'icon' => 'edit',
            'size' => 'md',
            'attributeList' => [
                'data-open' => 'modal-' . $privateModuleMetaKey,
                'style' => 'cursor: pointer;'
            ]
        ]
    ])

    @modal([
        'id' => 'modal-' . $privateModuleMetaKey,
        'size' => 'sm',
        'padding' => 4,
        'heading' => $lang['changeContent']
    ])
    @group([
        'direction' => 'vertical',
        'justifyContent' => 'center'
    ])
        @notice([
            'type' => 'error',
            'classList' => [
                'u-display--none',
                'u-print-display--none',
                'u-margin__bottom--2'
            ],
            'message' => ['text' => $lang['error']],
            'attributeList' => [
                'data-js-user-editable-error' => ''
            ],
            'icon' => [
                'name' => 'report',
                'size' => 'md',
                'color' => 'white'
            ]
        ])
        @endnotice
        @form([
            'classList' => [
                'u-print-display--none',
            ],
        ])
        @collection([
            'bordered' => true,
            'attributeList' => [
                'style' => 'border: none;'
            ]
        ])
            @foreach ($filteredManualInputs as $input)
                @collection__item([])
                    @slot('prefix')
                        <div class="c-collection__icon u-padding__left--0" @if(!empty($input['obligatory'])) data-tooltip="{{ $lang['obligatory'] }}" @endif>
                            @option([
                                'type' => 'checkbox',
                                'value' => $input['uniqueId'],
                                'label' => '',
                                'attributeList' => array_merge([
                                    'name' => $input['uniqueId']], 
                                    (!empty($input['obligatory']) ? ['disabled' => ''] : [])
                                ),
                                'checked' => $input['checked'],
                                'classList' => ['u-display--flex', (!empty($input['obligatory']) ? 'is-disabled' : '')]
                            ])
                            @endoption
                        </div>
                    @endslot
                    @typography([
                        'element' => 'h3',
                        'variant' => 'h3',
                    ])
                        {{ $input['title'] }}
                    @endtypography
                    @if (!empty($input['linkDescription']))
                        @typography([])
                            {{ $input['linkDescription'] }}
                        @endtypography
                    @endif
                @endcollection__item
            @endforeach
        @endcollection
        @endform
        @endgroup
        @slot('bottom')
        @group([
            'justifyContent' => 'flex-end',
            'gap' => 1
        ])
            @button([
                'text' => $lang['cancel'],
                'size' => 'md',
                'color' => 'default',
                'attributeList' => [
                    'data-close' => '',
                    'data-js-cancel-save' => ''
                ],
                'disableColor' => false,
            ])
            @endbutton
            @button([
                'text' => $lang['save'],
                'type' => 'submit',
                'size' => 'md',
                'color' => 'primary',
                'disableColor' => false,
                'attributeList' => [
                    'data-js-saving-lang' => $lang['saving'],
                ]
            ])
            @endbutton
        @endgroup
        @endslot
    @endmodal
@endelement
