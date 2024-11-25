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
        'user--active', 
        !empty($customizer->loginLogoutColorScheme) ? 'user--' . $customizer->loginLogoutColorScheme : '',
        $customizer->headerLoginLogoutBackgroundColorIsVisible ? 'user--has-background' : ''
    ], $classList ?? []),
    'context' => ['header.loginlogout', 'header.loginlogout.logout']
])

    @link([
        'href' => $logoutUrl,
        'classList' => [
            'user__link'
        ],
        'attributeList' => [
            'aria-label' => $lang->logout
        ]
    ])
        @avatar([
            'name' => $user->display_name ?? '',
            'size' => 'sm',
            'classList' => ['user__avatar']
        ])
        @endavatar
    @endlink
    
    <!-- Logout desktop -->
    @group([
        'direction' => 'vertical',
        'classList' => [
            'user__container',
            'u-display--none@xs'
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
            'classList' => ['user__link']
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

    <!-- Logout mobile -->
    @button([
        'text' => $lang->logout,
        'color' => 'basic',
        'style' => 'basic',
        'href' => $logoutUrl,
        'classList' => [
            'u-display--none@sm',
            'u-display--none@md',
            'u-display--none@lg',
            'u-display--none@xl',
            'u-display--none@xxl',
            'user__button'
        ],
    ])
    @endbutton
@endelement



