@if (!empty($menuItems))
    @nav([
        'classList' => [
            'c-nav--sidebar',            
            'u-visibility--hidden@xs',
            'u-visibility--hidden@sm',
        ],
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif