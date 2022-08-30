@if (!empty($mobileMenuItems))
@drawer([
    'classList' => ['c-drawer--' . (!empty($mobileMenuItems)&&!empty($mobileMenuSecondaryItems) ? 'duotone' : 'monotone')]
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
                        'c-nav--dark',
                        'site-nav-mobile__primary',
                    ]
                ]
            )  

                {{-- No ajax in wp-menus, thus not in its own file --}}

            @nav([
                    'classList' => [
                        'c-nav--drawer',
                        'c-nav--dark',
                        'site-nav-mobile__secondary'
                    ],
                    'items' => $mobileMenuSecondaryItems,
                    'direction' => 'vertical',
                    'includeToggle' => true
                ])
            @endnav  
    @endslot
      @else
      {{-- No menu items found --}}
      @endif

@enddrawer  
@endif