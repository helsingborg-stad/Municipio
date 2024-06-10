@if (!empty($menuItems))
    @nav([
        'id' => 'menu-mobile',
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'classList' => $classList,
        'depth' => $depth ?? 1,
        'expandLabel' => $lang->expand,
        'attributeList' => $attributeList ?? [],
        'title' => $title ?? null,

    ])
    @endnav
@endif
