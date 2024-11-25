
@element([
    'classList' => array_merge([
        'user', 
        'user--inactive', 
        !empty($customizer->loginLogoutColorScheme) ? 'user--' . $customizer->loginLogoutColorScheme : ''
    ], $classList ?? []),
    'context' => ['header.loginlogout', 'header.loginlogout.login']
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
        'href' => $logoutUrl,
        'classList' => [
            'u-display--none@xs',
            'user__button'
        ],
    ])
    @endbutton

@endelement



