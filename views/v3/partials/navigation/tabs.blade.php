@if($tabMenuItems)
    @nav([
        'id' => 'tabs',
        'items' => $tabMenuItems,
        'direction' => 'horizontal',
        'includeToggle' => false,
        'allowStyle' => true,
        'buttonColor' => $customizer->tabmenuButtonColor,
        'buttonStyle' => $customizer->tabmenuButtonType,
        'height' => 'sm',
        'classList' => [
            'u-width--auto'
        ]
    ])
    @endnav
@endif