@fab([
    'position' => 'bottom-left',
    'spacing' => 'md',
    classList => ['c-dropdown__toggle', 'js-dropdown__toggle',
'c-dropdown__toggle--rotate-plus', 'hidden-print']
btn btn-floating ',
    'button' => [
        'href' => '#btn-3',
        'background' => 'primary',
        'isIconButton' => true,
        'icon' => [
            'name' => 'add_box',
            'color' => 'white',
            'size' => 'lg'
        ],
classList => '',
        'reverseIcon' => true,
        'size' => 'lg',
        'color' => 'secondary',
        'floating' => [
            'animate' => false,
            'hover' => true
        ],
    ]
    ])
@endfab

{!! $fab['menu'] !!}

