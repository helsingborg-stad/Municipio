@element([
    'classList' => array_merge([
        'user', 
        'user--inactive', 
        !empty($customizer->loginLogoutColorScheme) ? 'user--' . $customizer->loginLogoutColorScheme : '',
        $loginLogoutHasBackgroundColor ? 'user--has-background' : ''
    ], $classList ?? []),
    'context' => ['header.loginlogout', 'header.loginlogout.login'],
    'attributeList' => [
        'data-js-sizeobserver' => 'user-background', 
        'data-js-sizeobserver-axis' => 'x', 
        'data-js-sizeobserver-use-box-size' => ''
    ]
])
    @link([
        'href' => $loginUrl,
        'classList' => [
            'user__link',
            'js-action-login-click'
        ],
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
            'user__button',
            'js-action-login-click'
        ],
    ])
    @endbutton

@endelement



