@if (!empty($hamburgerMenuItems))
    <div class="o-container o-container--wide">
        @hamburgerMenu([
            'menuItems' => $hamburgerMenuItems,
            'showSearch' => $showHamburgerMenuSearch,
            'parentButtons' => $customizer->hamburgerMenuParentButtons,
            'mobile' => $customizer->hamburgerMenuMobile,
        ])
        @endhamburgerMenu
    </div>
@endif
