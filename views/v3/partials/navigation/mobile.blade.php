@if (!empty($menuItems)) 
    @nav([
        'id' => 'menu-mobile',
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'classList' => ($classList ? $classList : null),
        'depth' => $depth ?? 0,
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif
