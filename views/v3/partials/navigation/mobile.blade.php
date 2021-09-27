@if (!empty($menuItems)) 
    @nav([
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'classList' => ($classList ? $classList : null)
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif
