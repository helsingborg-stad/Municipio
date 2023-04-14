@if($tabMenuItems)
    @nav([
        'id' => 'tabs',
        'items' => $tabMenuItems,
        'direction' => 'horizontal',
        'includeToggle' => false,
        'allowStyle' => true,
        'buttonColor' => 'default',
        'height' => 'sm',
        'classList' => [
            'u-width--auto'
        ]
    ])
    @endnav
@endif