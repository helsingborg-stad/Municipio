@if (!empty($hamburgerMenuItems))
    @button([
        'id' => 'hamburger-menu-trigger-open',
        'color' => 'default',
        'style' => $customizer->hamburgerMenuTriggerStyle ?? 'basic',
        'icon' => 'menu',
        'text' => $lang->menu,
        'classList' => 
            !$customizer->hamburgerMenuMobile ? 
            ['hamburger-menu-trigger','u-display--none@xs','u-display--none@sm','u-display--none@md'] 
            : 
            ['hamburger-menu-trigger'] 
        ,
        'classListText' => [
            'u-display--none@xs'
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
