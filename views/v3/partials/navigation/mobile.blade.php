@if (!empty($menuItems)) 
    @nav([
        'id' => 'menu-mobile',
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'classList' => ($classList ? $classList : null),
        'depth' => $depth ?? 1,
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif
