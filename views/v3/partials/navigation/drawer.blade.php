@if (!empty($mobileMenuItems))
@drawer([
    'toggleButtonData' => [
        'id' => 'mobile-menu-trigger-open',
        'color' => $customizer->headerTriggerButtonColor,
        'style' => $customizer->headerTriggerButtonType,
        'size' => $customizer->headerTriggerButtonSize,
        'icon' => 'menu',
        'context' => ['site.header.menutrigger', 'site.header.casual.menutrigger'],
        'classList' => ['mobile-menu-trigger', 'u-order--10'],
        'text' => $lang->menu,
        'reversePositions' => true,
        'toggle' => true,
        'attributeList' => [
            'data-toggle-icon' => 'close',
            'data-js-toggle-group' => 'drawer'
        ]
    ],
    'id' => 'drawer',
    'attributeList' => ['data-js-toggle-item' => 'drawer'],
    'classList' => [
        'c-drawer--' . (!empty($mobileMenuItems)&&!empty($mobileMenuSecondaryItems) ? 'duotone' : 'monotone'),
        's-drawer-menu'
    ],
    'label' => $lang->close,
    'screenSizes' => $customizer->drawerScreenSizes
])

    @slot('search')
        @includeWhen(
                $showMobileSearchDrawer,
                'partials.search.mobile-search-form',
                ['classList' => ['search-form', 'u-margin__top--2', 'u-width--100']]
            )
    @endslot

    @if (!empty($mobileMenuItems)||!empty($mobileMenuSecondaryItems)) 
    @slot('menu')
        @includeIf(
            'partials.navigation.mobile', 
                [
                    'menuItems' => $mobileMenuItems, 
                    'classList' => [
                        'c-nav--drawer',
                        'site-nav-mobile__primary',
                        's-nav-drawer',
                        's-nav-drawer-primary'
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
                        's-nav-drawer-secondary'
                    ],
                    'items' => $mobileMenuSecondaryItems,
                    'direction' => 'vertical',
                    'includeToggle' => true,
                    'height' => 'sm',
                    'expandLabel' => $lang->expand
                ])
            @endnav  
        @endslot
      @else
      {{-- No menu items found --}}
      @endif

@enddrawer  
@endif