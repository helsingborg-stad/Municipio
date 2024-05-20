@if (empty($menuItems))
@elseif(is_object($menuItems))
    @foreach($menuItems as $menu)
        @includeWhen(!empty($menu['items']), 'partials.navigation.mobile',
        [
            'menuItems' => $menu['items'],
            'attributeList' => $menu['attributeList'] ?? [],
            'title' => $menu['title'] ?? null,
            'classList' => [
                'c-nav--drawer',
                'site-nav-mobile__primary',
                's-nav-drawer',
                's-nav-drawer-primary'
            ]
        ])
    @endforeach
@else
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
