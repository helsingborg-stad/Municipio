@if ($customizer->hamburgerMenuEnabled && !empty($hamburgerMenuItems))
    @button([
        'id' => 'hamburger-menu-trigger-open',
        'color' => 'default',
        'style' => 'basic',
        'icon' => 'menu',
        'text' => '<span class="hamburger-menu-trigger__label">' . __('Menu', 'component-library') . '</span><span class="hamburger-menu-trigger__close">' . __('Close', 'component-library') . '</span>',
        'classList' => [
            'hamburger-menu-trigger',
        ],
        'attributeList' => [
            'aria-label' => $lang->menu,
            'aria-controls' => "navigation",
            'data-js-toggle-trigger' => 'hamburger-menu',
            'data-js-toggle-item' => 'hamburger-menu',
            'data-js-toggle-class' => 'open'
        ],
        'context' => $context
    ])
    @endbutton
@endif
