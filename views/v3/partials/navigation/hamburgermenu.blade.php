@if (!empty($hamburgerMenuItems))
    <div class="o-container o-container--wide">
        @hamburgerMenu([
            'menuItems' => $hamburgerMenuItems,
            'showSearch' => $showHamburgerMenuSearch,
            'parentStyle' => $customizer->hamburgerMenuParentStyle,
            'mobile' => $customizer->hamburgerMenuMobile,
        ])
        @endhamburgerMenu
    </div>
@endif
