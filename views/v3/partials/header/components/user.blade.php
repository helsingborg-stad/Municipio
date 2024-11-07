@if (!empty($customizer->headerLoginLogout))
    @if ($isAuthenticated)
        <div id="user-logout">
            @signature([
                'author' => $user->display_name ?? '',
                'avatar_size' => 'sm',
            ])
                @link([
                    'href' => $logoutUrl,
                ])
                    @icon([
                        'label' => $lang->logout,
                        'icon' => 'logout',
                        'size' => 'sm',
                    ])
                    @endicon
                    @typography([
                        'element' => 'span',
                        'variant' => 'body',
                    ])
                        {{ $lang->logout }}
                    @endtypography
                @endlink
            @endsignature
        </div>
        @elseif ($customizer->headerLoginLogout ===  'both')
        <div id="user-login">
            @link([
                'href' => $loginUrl,
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
        </div>
        @else 
    @endif
@endif