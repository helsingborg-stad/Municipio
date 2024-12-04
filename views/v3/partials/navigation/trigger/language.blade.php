@button([
    'id' => 'site-language-menu-button',
    'text' => $lang->changeLanguage,
    'color' => $customizer->headerTriggerButtonColor,
    'style' => $customizer->headerTriggerButtonType,
    'size' => $customizer->headerTriggerButtonSize,
    'icon' => 'language',
    'reversePositions' => true,
    'toggle' => true,
    'classList' => [
        'site-language-menu-button'
    ],
    'attributeList' => [
        'js-toggle-trigger' => 'language-menu-toggle',
        'data-toggle-icon' => 'close',
        'data-js-click-away-remove-pressed' => ''
    ]
])
@endbutton