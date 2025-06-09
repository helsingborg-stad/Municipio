@element([
    'classList' => array_merge([
        'user', 
        'user--active', 
        !empty($customizer->loginLogoutColorScheme) ? 'user--' . $customizer->loginLogoutColorScheme : '',
        'u-print-display--none'
    ], $classList ?? []),
    'context' => ['header.loginlogout', 'header.loginlogout.logout'],
    'attributeList' => [
        'data-js-sizeobserver' => 'user-background', 
        'data-js-sizeobserver-axis' => 'x', 
        'data-js-sizeobserver-use-box-size' => ''
    ]
])

    @avatar([
        'name' => $user->display_name ?? '',
        'size' => 'sm',
        'classList' => ['user__avatar']
    ])
    @endavatar
    
    <!-- Logout desktop -->
    @group([
        'direction' => 'vertical',
        'classList' => [
            'user__container'
        ]
    ])
        @typography([
                'element' => 'span',
                'classList' => [
                    'user__name'
                ]
            ])
                {{ $user->display_name ?? '' }}
        @endtypography

        @link([
            'href' => $logoutUrl,
            'classList' => [
                'user__link',
                'js-action-logout-click'
            ]
        ])
            @icon([
                'label' => $lang->logout,
                'icon' => 'logout',
                'size' => 'sm',
            ])
            @endicon
            @typography([
                'element' => 'span',
                'classList' => ['user__link-text'],
            ])
                {{ $lang->logout }}
            @endtypography
        @endlink
    @endgroup
@endelement
<?php 
var_dump($userGroup); ?> 