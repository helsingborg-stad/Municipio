@if (!empty($menuItems)) 
    @nav([
        'classList' => [
            'c-nav--drawer',
            'c-nav--dark',
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
        ],
        'items' => $secondaryMenuItems,
        'direction' => 'vertical',
        'includeToggle' => true
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif