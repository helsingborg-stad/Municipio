@if (!empty($customizer->headerLoginLogoutDisplay))
    @includeWhen(
        $isAuthenticated, 
        'partials.header.user.user', 
        ['classList' => $classList ?? [], 'buttonAppearance' => $buttonAppearance ?? null]
    )
    
    @includeWhen(
        !$isAuthenticated && $customizer->headerLoginLogoutDisplay ===  'both', 
        'partials.header.user.login', 
        ['classList' => $classList ?? [], 'buttonAppearance' => $buttonAppearance ?? null]
    )
@endif