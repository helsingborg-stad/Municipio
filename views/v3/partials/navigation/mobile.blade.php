@if (!empty($menuItems)) 
    @nav([
        'classList' => [
            'c-nav--drawer',
            'c-nav--dark',
            'site-nav-mobile__primary'
        ],
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true
    ])
    @endnav
    @nav([
        'classList' => [
            'c-nav--drawer',
            'c-nav--dark',
            'site-nav-mobile__secondary'
        ],
        'items' => $secondaryMenuItems,
        'direction' => 'vertical',
        'includeToggle' => true
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif