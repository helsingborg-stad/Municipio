@if(!empty($tabMenu['items']))

    @nav([
        'id' => 'tabs',
        'items' => $tabMenu['items'],
        'direction' => 'horizontal',
        'includeToggle' => false,
        'allowStyle' => true,
        'buttonColor' => $customizer->tabmenuButtonColor,
        'buttonStyle' => $customizer->tabmenuButtonType,
        'classList' => [
            'u-width--auto',
            'u-display--none@xs',
            'u-display--none@sm',
            'u-display--none@md'
        ]
    ])
    @endnav
@endif