@form([
    'action' => $formAction,
    'classList' => [
        'post-password-form'
    ]
])
    @if($messageBefore)
        @typography([
        'classList'     => ['u-margin__bottom', 'u-margin__top'],
        ])
        @icon([
            'icon' => 'lock',
        ])
        @endicon
        {{ $messageBefore }}
        @endtypography
    @endif
    @field([
        'type'          => 'password',
        'name'          => 'post_password',
        'label'         => $passwordFieldLabel,
        'required'      => true,
        'size'          => 'md',
        'classList'     => ['u-margin__bottom', 'u-margin__top'],
        'attributeList' => ['size' => 20],
    ])
    @endfield
    @button([
        'text' => $submitBtnValue,
        'color' => 'secondary',
    ])
    @endbutton

@endform
