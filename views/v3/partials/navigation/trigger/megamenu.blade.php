@if (!empty($megaMenu['items']))
    @button([
        'color' => $customizer->headerTriggerButtonColor,
        'style' => $customizer->headerTriggerButtonType,
        'size' => $customizer->headerTriggerButtonSize,
        'reversePositions' => empty($megaMenuLabels->iconAfterLabel),
        'toggle' => true,
        'icon' => !empty($megaMenuLabels->buttonIcon) ? $megaMenuLabels->buttonIcon : 'menu',
        'text' => !empty($megaMenuLabels->buttonLabel) ? $megaMenuLabels->buttonLabel : $lang->menu,
        'classList' => array_merge($classList ??
            (!$customizer->megaMenuMobile ? 
            ['mega-menu-trigger','u-display--none@xs','u-display--none@sm','u-display--none@md'] : 
            ['mega-menu-trigger']), ['s-header-button'])
        ,
        'classListText' => [
            'u-display--none@xs',
        ],
        'attributeList' => [
            'aria-label' => $lang->primaryNavigation,
            'aria-controls' => 'mega-menu',
            'data-toggle-icon' => 'close',
            'data-js-mega-menu-trigger' => 'mega-menu'
        ],
        'context' => $context
    ])
    @endbutton
@endif
