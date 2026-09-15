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
        'site-language-menu-button',
        's-header-button'
    ],
    'attributeList' => [
        'popovertarget' => 'site-language-menu-popover',
        'popovertargetaction' => 'toggle'
    ]
])
@endbutton