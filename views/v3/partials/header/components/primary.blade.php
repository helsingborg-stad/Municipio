@if (!empty($primaryMenu['items']))
    @nav([
        'id' => 'menu-primary',
        'items' => $primaryMenu['items'],
        'allowStyle' => true,
        'direction' => 'horizontal',
        'classList' => array_merge(
            $menuItem->getCssClasses(),
            ['s-nav-primary', 'u-flex-wrap--no-wrap']
        ),
        'depth' => $depth ?? 1,
        'context' => ['site.header.nav'],
        'height' => 'lg',
        'expandLabel' => $lang->expand,
        'includeToggle' => $customizer->primaryMenuDropdown ?? false,
        'isExtendedDropdown' => $customizer->primaryMenuDropdown && $customizer->primaryMenuDropdownExtended
    ])
    @endnav
@endif