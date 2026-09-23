@button([
    'id' => 'site-language-menu-button',
    'text' => $lang->changeLanguage,
    'color' => $buttonAppearance['color'],
    'style' => $buttonAppearance['style'],
    'size' => $buttonAppearance['size'],
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