@if ($customizer->hamburgerMenuEnabled && !empty($hamburgerMenuItems))
    @button([
        'id' => 'hamburger-menu-trigger-open',
        'color' => 'default',
        'style' => 'basic',
        'icon' => 'menu',
        'text' => __('Menu', 'component-library'),
        'classList' => [
            'hamburger-menu-trigger',
        ],
        'attributeList' => [
            'aria-label' => $lang->menu,
            'aria-controls' => "navigation",
            'data-js-toggle-trigger' => 'hamburger-menu',
            'data-toggle-icon' => 'close',
            'data-toggle-label' => __('Close', 'component-library'),
        ],
        'context' => $context
    ])
    @endbutton
@endif
