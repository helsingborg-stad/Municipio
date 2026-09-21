@if(!empty($menuItem['label']))
    @link([
        'href' => $menuItem['href'] ?? '#',
        'classList' => [
            'mod-menu__heading-item',
        ]
    ])
        @if(!empty($menuItem['icon']['icon']))
            @icon([
                'icon' => $menuItem['icon']['icon'] ?? '',
                'size' => 'lg',
                'classList' => [
                    'mod-menu__heading-icon',
                    'u-color__text--primary'
                ],
            ])
            @endicon
        @endif
        @typography([
            'element' => 'h2',
            'variant' => 'h4',
            'classList' => [
                'mod-menu__heading-label',
                'u-margin__y--0'
            ]
        ])
            {{$menuItem['label'] ?? ""}}
        @endtypography
    @endlink
@endif
