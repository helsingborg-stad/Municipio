@nav([
    'items' => $menuItem['children'],
    'compressed' => true,
    'classList' => [
        'mod-menu__children'
    ],
    'attributeList' => $mobileCollapse 
        ? [
            'data-js-sizeobserver' => 'mod-menu-children',
            'data-js-sizeobserver-axis' => 'y',
            'data-js-sizeobserver-element-full-size' => ''
        ]
        : []
])
@endnav