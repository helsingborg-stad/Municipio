
@if(count($floatingMenuItems) == 1)
    <!-- Single link item -->
    @button([
        'id' => 'fab-item',
        'classList' => ['u-fixed--bottom-right', 'u-margin--2'],
        'icon' => !empty($floatingMenuItems[0]['icon']['icon']) ? $floatingMenuItems[0]['icon']['icon'] : $floatingMenuLabels->buttonIcon,
        'size' => 'md',
        'color' => 'primary',
        'text' => $floatingMenuItems[0]['label'] ? $floatingMenuItems[0]['label'] : $floatingMenuLabels->buttonLabel,
        'reversePositions' => true,
        'href' => $floatingMenuItems[0]['href']
    ])
    @endbutton
@else
    <!-- Multiple link items -->
    @fab([
        'id' => 'fab-item',
        'position' => 'bottom-right',
        'heading' => $floatingMenuLabels->heading,
        'button' => [
            'icon' => $floatingMenuLabels->buttonIcon,
            'size' => 'md',
            'color' => 'primary',
            'text' => $floatingMenuLabels->buttonLabel,
            'reversePositions' => true,
        ],
        'closeLabel' => $lang->close,
        'closeIcon' =>'close',
        'context' => ['site.navigation.floating']
    ])
        @nav([
            'id' => 'menu-floating',
            'items' => $floatingMenuItems,
            'direction' => 'vertical',
            'includeToggle' => false,
            'classList' => ['c-nav--tiles'],
            'context' => ['site.navigation.floating.nav'],
            'height' => 'md'
        ])
        @endnav
    @endfab
@endif
