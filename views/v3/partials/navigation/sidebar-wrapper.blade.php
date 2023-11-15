@if ($customizer->secondaryNavigationPosition == $position)
    @if ($secondaryMenuItems)
        <div class="u-margin__bottom--4 u-display--none@xs u-display--none@sm u-display--none@md">
            @paper()
                @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenuItems])
            @endpaper </div>
    @endif
@endif
