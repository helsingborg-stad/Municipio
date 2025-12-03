@group([
    'direction' => 'vertical',
    'alignItems' => $align,
])
    @if (empty($hideTitle) && !empty($postTitle))
        @typography([
            'id'        => 'mod-search-' . $ID .'-label',
            'element'   => 'h2', 
            'variant'   => 'h2', 
            'classList' => [
                'module-title'
            ]
        ])
            {!! $postTitle !!}
        @endtypography
    @endif

    @form([
        'method'    => 'get',
        'action'    => $homeUrl,
        'classList' => ['search-form', 'c-form--hidden', 'u-box-shadow--5', 'u-print-display--none'],
        'context' => ['module.search'],
        'attributeList' => [
            'style' => 'width: max(' . $width . '%, 300px);'
        ]
    ])
        @group([])
            @field([
                'type' => 'search',
                'name' => 's',
                'required' => false,
                'placeholder' => $placeholder,
                'size' => 'lg',
                'radius' => 'xs',
                'icon' => [
                    'icon' => 'search', 
                    'classList' => ['u-display--none@xs', 'c-field__icon']
                ]
            ])
            @endfield
            @button([
                'text' => $buttonLabel,
                'type' => 'submit',
                'size' => 'md',
                'color' => 'primary',
                'attributeList' => [
                ],
                'disableColor' => false,
                'context' => ['module.search.button'],
                'classList' => [
                    'u-height--unset',
                    'u-padding__x--1'
                ]
            ])
            @endbutton
        @endgroup
    @endform
@endgroup
