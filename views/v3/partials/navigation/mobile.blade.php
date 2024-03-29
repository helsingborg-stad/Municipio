@if (!empty($menuItems)) 
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
@else
    {{-- No menu items found --}}
@endif
