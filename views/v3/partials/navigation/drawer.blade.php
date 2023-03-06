@if (!empty($mobileMenuItems))
@drawer([
    'id' => 'drawer',
    'classList' => [
        'c-drawer--' . (!empty($mobileMenuItems)&&!empty($mobileMenuSecondaryItems) ? 'duotone' : 'monotone'),
        !$customizer->showMobileMenuOnAllScreens ? ' u-display--none@lg' : ''
    ],
    'label' => $lang->close
])

    @slot('search')
        @includeWhen(
                $showMobileSearchDrawer,
                'partials.search.mobile-search-form',
                ['classList' => ['u-margin__top--2', 'u-width--100']]
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
                        's-nav-drawer-prmary'
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