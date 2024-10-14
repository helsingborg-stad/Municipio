@if (!empty($megaMenu['items']))
    @button([
        'id' => 'mega-menu-trigger-open',
        'color' => $customizer->headerTriggerButtonColor,
        'style' => $customizer->headerTriggerButtonType,
        'size' => $customizer->headerTriggerButtonSize,
        'reversePositions' => empty($megaMenuLabels->iconAfterLabel),
        'toggle' => true,
        'icon' => !empty($megaMenuLabels->buttonIcon) ? $megaMenuLabels->buttonIcon : 'menu',
        'text' => !empty($megaMenuLabels->buttonLabel) ? $megaMenuLabels->buttonLabel : $lang->menu,
        'classList' => $classList ??
            (!$customizer->megaMenuMobile ? 
            ['mega-menu-trigger','u-display--none@xs','u-display--none@sm','u-display--none@md'] 
            : 
            ['mega-menu-trigger'])
        ,
        'classListText' => [
            'u-display--none@xs',
            'u-order--10'
        ],
        'attributeList' => [
            'aria-label' => $lang->primaryNavigation,
            'aria-controls' => "mega-menu",
            'data-js-toggle-trigger' => 'mega-menu',
            'data-toggle-icon' => 'close'
        ],
        'context' => $context
    ])
    @endbutton
@endif
