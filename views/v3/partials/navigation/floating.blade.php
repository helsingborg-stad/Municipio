@fab([
    'position' => 'bottom-left',
    'spacing' => 'md',
    'button' => [
        'icon' => 'close',
        'size' => 'lg',
        'color' => 'secondary'
    ],
    'classList' => ['d-fab__left','u-position--static']
])

    @nav([
        'classList' => [
            'c-nav--sidebar',            
            'c-nav--bordered',
            'u-margin--2',
            '.u-print-display--none'
        ],
        'items' => $floatingMenuItems,
        'direction' => 'vertical',
        'includeToggle' => false
    ])
    @endnav
    

    {{-- Toggle button --}}
    @button([
        'type' => 'filled',
        'icon' => 'close',
        'size' => 'lg',
        'text' => 'Right',
        'color' => 'primary',
        'label' => 'test'
    ])
    @endbutton
        
@endfab