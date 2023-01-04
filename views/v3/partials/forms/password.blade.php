@form([
    'action' => $formAction,
    'classList' => [
        'post-password-form'
    ]
])
    @if($messageBefore)
        @notice([
            'type' => 'info',
            'message' => [
            'text' => $messageBefore,
            'size' => 'md'
            ],
            'icon' => [
                'name' => 'lock',
            ],
            'classList' => ['u-margin__bottom--3']
        ])
        @endnotice
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
        'type' => 'submit',
    ])
    @endbutton

@endform
