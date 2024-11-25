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
        'classList' => ['user__link']
    ])
        @icon([
            'label' => $lang->login,
            'icon' => 'login',
            'size' => 'sm',
        ])
        @endicon
        @typography([
            'element' => 'span',
            'variant' => 'body',
        ])
            {{ $lang->login }}
        @endtypography
    @endlink
@endelement