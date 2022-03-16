@if ($customizer->hamburgerMenuEnabled && !empty($hamburgerMenuItems))
    @hamburgerMenu([
        'menuItems' => $hamburgerMenuItems,
        'showSearch' => $showHamburgerMenuSearch,
        'parentButtons' => $customizer->hamburgerMenuParentButtons,
        'classList' => [
            'u-display--none',
            !$customizer->hamburgerMenuMobile ? 'u-display--none@sm u-display--none@md' : '',
        ]
    ])
    @endhamburgerMenu
@endif
