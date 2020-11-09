@if (!empty($menuItems)) 
    @nav([
        'classList' => [
            'c-nav--drawer', 
            'c-nav--bordered'
        ],
            'items' => $menuItems,
            'direction' => 'vertical',
            'includeToggle' => true
        ])
    @endnav
@else
    {{-- No menu items found --}}
@endif