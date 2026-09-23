@link([
    'href' => $child['href'],
    'xfn' => $child['xfn'],
    'classList' => [
        'mod-menu__child-item'
    ]
])
    @element([
        'componentElement' => 'span'
    ])
        @element([
            'componentElement' => 'span',
            'classList' => [
                'mod-menu__child-label'
            ]
        ])
            {{ $child['label'] }}
        @endelement
        @icon([
            'icon' => 'arrow_forward',
            'size' => 'sm',
            'attributeList' => [
                'style' => 'vertical-align: middle;'
            ]
        ])
        @endicon
    @endelement
@endlink