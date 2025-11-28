@nav([
    'classList' => ['preloader'],
    'depth' => $depth ? $depth + 1 : 2,
    'items' => [
        [
            'href' => '#',
            'label' => '',
            'children' => false,
            'ID' => PHP_INT_MAX,
            'classList' => [
                'u-preloader',
                'u-preloader__opacity--9'
            ]
        ]
    ]
])
@endnav