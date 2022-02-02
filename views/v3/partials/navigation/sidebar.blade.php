@if (!empty($menuItems))
    @paper()
        @nav([
            'classList' => [
                'c-nav--sidebar',            
                'c-nav--bordered',            
                'u-display--none@xs',
                'u-display--none@sm',
                'u-print-display--none'
            ],
            'items' => $menuItems,
            'direction' => 'vertical',
            'includeToggle' => true,
            'depth' => $depth ?? 0,
        ])
        @endnav
    @endpaper
@else
    {{-- No menu items found --}}
@endif