@link([
    'href' => $menuItem['href'] ?? '#',
    'classList' => [
        'mod-menu__heading-link',
    ]
])
    @typography([
        'element' => 'h2',
        'variant' => 'h4',
        'classList' => [
            'mod-menu__heading-label',
            'u-margin__top--0'
        ]
    ])
        {{$menuItem['label'] ?? ""}}
    @endtypography
@endlink
