@if($customizer->headerLoginLogoutBackgroundColorIsVisible) 
    <style>
        .user {
            --user-background-color: {{ $customizer->headerLoginLogoutBackgroundColor ?? ''}};
        }
    </style>
@endif

@element([
    'classList' => array_merge([
        'user', 
        'user--inactive', 
        !empty($customizer->loginLogoutColorScheme) ? 'user--' . $customizer->loginLogoutColorScheme : '',
        $customizer->headerLoginLogoutBackgroundColorIsVisible ? 'user--has-background' : ''
    ], $classList ?? []),
    'context' => ['header.loginlogout', 'header.loginlogout.login'],
    'attributeList' => ['data-js-sizeobserver' => '']
])
    @link([
        'href' => $loginUrl,
        'classList' => ['user__link'],
        'attributeList' => [
            'aria-label' => $lang->login
        ]
    ])
        @avatar([
            'icon' => ['name' => 'person', 'size'=> 'md'],
            'size' => 'sm',
            'classList' => ['user__avatar']
        ])
        @endavatar
    @endlink

    @button([
        'text' => $lang->login,
        'color' => 'basic',
        'style' => 'basic',
        'href' => $loginUrl,
        'classList' => [
            'u-display--none@xs',
            'u-display--none@sm',
            'user__button'
        ],
    ])
    @endbutton

@endelement



