{{-- @dump($customizer) --}}
@if (!empty($customizer->headerLoginLogoutDisplay))
    @includeWhen($isAuthenticated, 'partials.header.user.user')
    @includeWhen(!$isAuthenticated && $customizer->headerLoginLogoutDisplay ===  'both', 'partials.header.user.login')
@endif