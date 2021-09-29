<div class="c-drawer c-drawer--right c-drawer--primary js-drawer u-display--none@lg {{'c-drawer--' . $customize->mobilemenu->mobileMenuStyle }}" data-js-toggle-item="js-drawer">
    <div class="c-drawer__header">
        <button class="hamburger hamburger--drawer hamburger--stacked@sm hamburger--reverse@md hamburger--slider is-active js-close-drawer" type="button"
        aria-label="Menu" aria-controls="navigation">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
            <span class="hamburger-label">
                {{ $lang->close }}
            </span>
        </button>
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
                        'site-nav-mobile__primary'
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