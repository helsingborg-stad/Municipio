@if (!empty($menuItems))
    @nav([
        'id' => 'menu-sidebar', 
        'classList' => [
            'c-nav--sidebar',            
            'c-nav--bordered',
            'u-print-display--none',
            's-nav-sidebar'
        ],
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'depth' => $depth ?? 1,
        'context' => ['sidebar', 'municipio.sidebar', 'municipio.menu.vertical'],
        'height' => 'sm',
        'expandLabel' => $lang->expand
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif