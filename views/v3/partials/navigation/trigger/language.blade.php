@button([
    'id' => 'site-language-menu-button',
    'text' => $lang->changeLanguage,
    'color' => 'default',
    'style' => 'basic',
    'icon' => 'language',
    'reversePositions' => true,
    'classList' => [
        'site-language-menu-button'
    ],
    'attributeList' => [
        'js-toggle-trigger' => 'language-menu-toggle'
    ]
])
@endbutton