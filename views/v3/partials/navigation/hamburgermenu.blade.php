@if ($customizer->hamburgerMenuEnabled && !empty($hamburgerMenuItems))
    @hamburgerMenu([
        'menuItems' => $hamburgerMenuItems,
        'showSearch' => $showHamburgerMenuSearch,
        'parentButtons' => $customizer->hamburgerMenuParentButtons,
        'mobile' => $customizer->hamburgerMenuMobile,
    ])
    @endhamburgerMenu
@endif
