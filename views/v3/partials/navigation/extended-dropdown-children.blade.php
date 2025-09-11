@nav([
    'items' => $menuItems ?? [],
    'includeToggle' => true,
    'depth' =>  $depth ? $depth + 1 : 2,
    'direction' => 'vertical',
    'height' => 'sm',
    'classList' => [
        'c-nav--bordered',
        'c-nav__extended-child-menu'
    ]
])
@endnav