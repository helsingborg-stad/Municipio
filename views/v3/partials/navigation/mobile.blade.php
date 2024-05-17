@if (empty($menuItems))
@elseif(is_object($menuItems))
    @foreach($menuItems as $menu)
        @includeWhen(!empty($menu['items']), 'partials.navigation.mobile',
        [
            'menuItems' => $menu['items'],
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
        'expandLabel' => $lang->expand
    ])
    @endnav
@endif
