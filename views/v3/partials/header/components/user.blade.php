@if (!empty($customizer->headerLoginLogoutDisplay))
    @includeWhen(
        $isAuthenticated, 
        'partials.header.user.user', 
        ['classList' => $classList ?? []]
    )
    
    @includeWhen(
        !$isAuthenticated && $customizer->headerLoginLogoutDisplay ===  'both', 
        'partials.header.user.login', 
        ['classList' => $classList ?? []]
    )
@else
    @includeWhen(
        $isAuthenticated && !$isAdminBarShowing, 
        'partials.header.user.user', 
        ['classList' => $classList ?? []]
    )
@endif