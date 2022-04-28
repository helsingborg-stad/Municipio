@if (!empty($hamburgerMenuItems))
    @button([
        'id' => 'hamburger-menu-trigger-open',
        'color' => 'primary',
        'style' => $customizer->hamburgerMenuTriggerStyle ?? 'basic',
        'icon' => 'menu',
        'text' => $lang->menu,
        'classList' => [
            'hamburger-menu-trigger',
            !$customizer->hamburgerMenuMobile ? 'u-display--none@xs u-display--none@sm u-display--none@md' : '',
        ],
        'attributeList' => [
            'aria-label' => $lang->primaryNavigation,
            'aria-controls' => "navigation",
            'data-js-toggle-trigger' => 'hamburger-menu',
            'data-toggle-icon' => 'close',
            'data-toggle-label' => $lang->close,
        ],
        'context' => $context
    ])
    @endbutton
@endif
