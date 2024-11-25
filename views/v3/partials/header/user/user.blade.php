@if($customizer->headerLoginLogoutBackgroundColorIsVisible) 
    <style>
        .user {
            --user-active-background-color: {{ $customizer->headerLoginLogoutBackgroundColor ?? ''}};
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

    @dropdown([
        'items' => [['text' => 'cool', 'link' => '#']],
        'direction' => 'left',
        'popup' => 'click'
    ])
        @avatar([
            'name' => $user->display_name ?? '',
            'size' => 'sm',
            'classList' => ['user__avatar']
        ])
        @endavatar
    @enddropdown
    
    @group([
        'direction' => 'vertical',
        'classList' => ['user__container']
    ])
        @typography([
                'element' => 'span',
                'classList' => ['user__name']
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
@endelement



