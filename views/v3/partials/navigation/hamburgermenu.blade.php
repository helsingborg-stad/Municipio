@if (get_theme_mod('hamburger_menu_enabled') && !empty($hamburgerMenuItems))
    @hamburgerMenu([
        'menuItems' => $hamburgerMenuItems,
        'showSearch' => $showHamburgerMenuSearch,
        'parentButtons' => get_theme_mod('hamburger_menu_parent_buttons'),
        'classList' => ['u-display--none']
    ])
    @endhamburgerMenu
@endif