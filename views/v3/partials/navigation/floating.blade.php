
@if(count($floatingMenuItems) == 1)
    <!-- Single link item -->
    @button([
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
            'items' => $floatingMenuItems,
            'direction' => 'vertical',
            'includeToggle' => false,
            'classList' => ['c-nav--tiles'],
            'context' => ['site.navigation.floating.nav']
        ])
        @endnav
    @endfab
@endif
