@if(!empty($menuItem['label']))
    @link([
        'href' => $menuItem['href'] ?? '#',
        'classList' => [
            'mod-menu__heading-item',
        ]
    ])
        @typography([
            'element' => 'h2',
            'variant' => 'h5',
            'classList' => [
                'mod-menu__heading-label',
                'u-margin__y--0',
                'u-color__text--primary'
            ]
        ])
            {{ $menuItem['label'] }}
            @icon([
                'icon' => 'arrow_forward',
                'size' => 'md',
                'attributeList' => [
                    'style' => 'vertical-align: middle;'
                ]
            ])
            @endicon
        @endtypography
    @endlink
@endif
