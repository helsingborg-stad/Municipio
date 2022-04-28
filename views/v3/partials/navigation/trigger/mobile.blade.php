@button([
    'id' => 'mobile-menu-trigger-open',
    'color' => 'default',
    'style' => 'basic',
    'icon' => 'menu',
    'classList' => [
        'mobile-menu-trigger',
        'u-display--none@lg'
    ],
    'attributeList' => [
        'aria-label' => $lang->menu,
        'aria-controls' => "navigation",
        'js-toggle-trigger' => 'js-drawer'
    ],
    'context' => ['site.header.menutrigger', 'site.header.casual.menutrigger']
])
@endbutton