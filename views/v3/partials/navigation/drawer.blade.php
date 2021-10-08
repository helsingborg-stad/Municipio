<div class="c-drawer c-drawer--right c-drawer--primary js-drawer u-display--none@lg {{'c-drawer--' . $customize->mobilemenu->mobileMenuStyle }}" js-toggle-class="is-open" js-toggle-item="js-drawer">
    <div class="c-drawer__header">

        @button([
            'id' => 'mobile-menu-trigger-close',
            'style' => 'basic',
            'icon' => 'close',
            'attributeList' => [
                'aria-controls' => 'navigation',
                'js-toggle-trigger' => 'js-drawer'
            ],
            'classList' => [
                'c-drawer__close',
                'u-display--none@lg'
            ],
            'size' => 'md',
            'text' => $lang->close
        ])
        @endbutton

        @includeWhen(
            $showMobileSearchDrawer,
            'partials.search.mobile-search-form',
            ['classList' => ['u-margin__top--2', 'u-width--100']]
        )

    </div>
    <div class="c-drawer__body">
        @if (!empty($mobileMenuItems)||!empty($mobileMenuSecondaryItems)) 
            
            {{-- Placed in another file, due to ajax loading --}}
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

        @else
            {{-- No menu items found --}}
        @endif
    </div>
</div>
<div class="drawer-overlay js-close-drawer u-display--none@lg"></div>