@if (!empty($menuItems))
    @paper(['classList' => ['u-print-display--none']])
        @nav([
            'classList' => [
                'c-nav--sidebar',            
                'c-nav--bordered'
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