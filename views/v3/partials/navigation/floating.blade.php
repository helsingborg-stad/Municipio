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