@if ($customizer->secondaryNavigationPosition == $position)
    @if (!empty($secondaryMenu['items']))
        <div class="u-margin__bottom--4 u-display--none@xs u-display--none@sm u-display--none@md">
            @paper()
                @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenu['items']])
            @endpaper </div>
    @endif
@endif
