@if (!empty($megaMenu['items']))
    @element([
        'classList' => array_merge([
            'site-mega-menu',
            'u-display--flex'
        ], $menuItem->getCssClasses())
    ])
        @button([
            'color' => $menuItem->getButtonColor(),
            'style' => $menuItem->getButtonStyle(),
            'size' => $menuItem->getButtonSize(),
            'reversePositions' => empty($megaMenuLabels->iconAfterLabel),
            'toggle' => true,
            'icon' => !empty($megaMenuLabels->buttonIcon) ? $megaMenuLabels->buttonIcon : 'menu',
            'text' => !empty($megaMenuLabels->buttonLabel) ? $megaMenuLabels->buttonLabel : $lang->menu,
            'classList' => ['mega-menu-trigger'],
            'attributeList' => [
                'aria-label' => $lang->primaryNavigation,
                'aria-controls' => 'mega-menu',
                'data-toggle-icon' => 'close',
                'data-js-mega-menu-trigger' => 'mega-menu'
            ],
            'context' => ['site.header.mega-menu-trigge']
        ])
        @endbutton
    @endelement
@endif
