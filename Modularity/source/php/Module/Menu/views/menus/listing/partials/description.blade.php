@if(!empty($menuItem['description']))
    @typography([
        'element' => 'span',
        'classList' => [
            'mod-menu__heading-description',
            'u-display--block'
        ]
    ])
        {{ $menuItem['description'] }}
    @endtypography
@endif
