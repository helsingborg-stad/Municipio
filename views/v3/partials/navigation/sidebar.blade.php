@if (!empty($menuItems))
    @nav([
        'id' => 'menu-sidebar', 
        'classList' => [
            'c-nav--sidebar',            
            'c-nav--bordered',
            'u-print-display--none'
        ],
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'depth' => $depth ?? 0,
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif