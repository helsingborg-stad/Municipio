@if (!empty($menuItems))
    @nav([
        'classList' => [
            'c-nav--sidebar',            
            'c-nav--bordered',            
            'u-display--none@xs',
            'u-display--none@sm',
            'u-print-display--none', 
            'u-margin__bottom--4'
        ],
        'items' => $menuItems,
        'direction' => 'vertical',
        'includeToggle' => true
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif