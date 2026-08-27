@if(!empty($tabMenu['items']))
    @nav([
        'id' => 'tabs',
        'items' => $tabMenu['items'],
        'direction' => 'horizontal',
        'includeToggle' => false,
        'allowStyle' => true,
        'buttonColor' => $customizer->tabmenuButtonColor,
        'buttonStyle' => $customizer->tabmenuButtonType,
        'height' => apply_filters('Municipio/Hook/headerSecondaryNavigationTabsHeight', 'sm', $customizer),
        'classList' => apply_filters('Municipio/Hook/headerSecondaryNavigationTabsClass', [
            'u-width--auto',
            'u-display--none@xs',
            'u-display--none@sm',
            'u-display--none@md'
        ], $customizer)
    ])
    @endnav
@endif
