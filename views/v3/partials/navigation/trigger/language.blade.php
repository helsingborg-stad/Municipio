@button([
    'id' => 'site-language-menu-button',
    'color' => 'default',
    'style' => 'basic',
    'icon' => 'language',
    'classList' => [
        'site-language-menu-button'
    ],
    'attributeList' => [
        'js-toggle-trigger' => 'language-menu-toggle',
        'aria-label' => __("Select language", 'municipio')
    ]
])
@endbutton