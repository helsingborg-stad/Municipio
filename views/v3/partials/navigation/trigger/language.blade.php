@button([
    'id' => 'site-language-menu-button',
    'text' => $lang->changeLanguage,
    'color' => $buttonAppearance['color'] ?? 'inherit',
    'style' => $buttonAppearance['style'] ?? 'basic',
    'size' => $buttonAppearance['size'] ?? 'md',
    'icon' => 'language',
    'reversePositions' => true,
    'toggle' => true,
    'classList' => [
        'site-language-menu-button',
        's-header-button'
    ],
    'attributeList' => [
        'popovertarget' => 'site-language-menu-popover',
        'popovertargetaction' => 'toggle'
    ]
])
@endbutton