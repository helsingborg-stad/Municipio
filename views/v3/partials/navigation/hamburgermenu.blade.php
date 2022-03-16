@if ($customizer->hamburgerMenuEnabled && !empty($hamburgerMenuItems))
    @hamburgerMenu([
        'menuItems' => $hamburgerMenuItems,
        'showSearch' => $showHamburgerMenuSearch,
        'parentButtons' => $customizer->hamburgerMenuParentButtons,
        'classList' => ['u-display--none']
    ])
    @endhamburgerMenu
@endif