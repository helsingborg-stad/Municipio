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
    'classList' => []
])

    @nav([
        'items' => $floatingMenuItems,
        'direction' => 'vertical',
        'includeToggle' => false,
        'classList' => ['c-nav--tiles']
    ])
    @endnav

@endfab