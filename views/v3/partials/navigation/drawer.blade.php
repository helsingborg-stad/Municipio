@if (!empty($mobileMenu['items']))
@drawer([
    'toggleButtonData' => [
        'id' => 'mobile-menu-trigger-open',
        'color' => $customizer->headerTriggerButtonColor,
        'style' => $customizer->headerTriggerButtonType,
        'size' => $customizer->headerTriggerButtonSize,
        'icon' => 'toggleAriaPressedHamburgerClose',
        'context' => ['site.header.menutrigger', 'site.header.casual.menutrigger'],
        'classList' => ['mobile-menu-trigger', 'u-order--10', 's-header-button'],
        'text' => $lang->menu,
        'reversePositions' => true,
        'toggle' => true,
        'attributeList' => [
            'data-toggle-icon' => 'close',
            'data-js-toggle-group' => 'drawer'
        ]
    ],
    'id' => 'drawer',
    'attributeList' => ['data-move-to' => 'body', 'data-js-toggle-item' => 'drawer'],
    'classList' => [
        'c-drawer--' . (!empty($mobileMenu['items'])&&!empty($mobileSecondaryMenu['items']) ? 'duotone' : 'monotone'),
        's-drawer-menu'
    ],
    'label' => $lang->close,
    'screenSizes' => $screenSizes ?? $customizer->drawerScreenSizes,
    'context' => ['site.header.drawer'],
])

    @slot('search')
        @includeWhen(
                $showMobileSearchDrawer,
                'partials.search.drawer-search-form',
                ['classList' => ['search-form', 'u-margin__top--2', 'u-width--100']]
            )
    @endslot

    @if (!empty($mobileMenu['items'])||!empty($mobileSecondaryMenu['items'])) 
    @slot('menu')
        @includeIf(
            'partials.navigation.mobile', 
                [
                    'mobileMenu' => $mobileMenu, 
                    'classList' => [
                        'c-nav--drawer',
                        'site-nav-mobile__primary',
                        's-nav-drawer',
                        's-nav-drawer-primary',
                        !empty($customizer->drawerDivider) ? 'c-nav--bordered' : '',
                        !empty($customizer->drawerDividerTopLevelOnly) ? 'c-nav--bordered-top-level' : ''
                        
                    ]
                ]
            )

                {{-- No ajax in wp-menus, thus not in its own file --}}

            @nav([
                    'id' => 'drawer-menu',
                    'classList' => [
                        'c-nav--drawer',
                        'site-nav-mobile__secondary',
                        's-nav-drawer',
                        's-nav-drawer-secondary',
                        !empty($customizer->drawerDivider) ? 'c-nav--bordered' : '',
                        !empty($customizer->drawerDividerTopLevelOnly) ? 'c-nav--bordered-top-level' : ''
                    ],
                    'items' => $mobileSecondaryMenu['items'],
                    'direction' => 'vertical',
                    'includeToggle' => true,
                    'height' => 'sm',
                    'expandLabel' => $lang->expand,
                    'context' => 'site.mobile-menu'
                ])
            @endnav  
        @endslot
        @if (!empty($customizer->headerLoginLogoutShowInMobileMenu))
            @slot('afterMenu')
                @include(
                    'partials.header.components.user',
                    [
                        'classList' => ['user--drawer']
                    ]
                )
            @endslot
        @endif
      @else
      {{-- No menu items found --}}
      @endif

@enddrawer  
@endif