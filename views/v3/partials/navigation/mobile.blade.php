@if (!empty($menuItems)) 
    @nav([
        'classList' => [
            'c-nav--drawer',
            'c-nav--dark',
        ],
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true,
        'aria-orientation' => 'vertical'
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif