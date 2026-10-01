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
        @element([
            'componentElement' => 'span',
            'classList' => [
                'mod-menu__child-icon'
            ]
        ])
            @element([
                'componentElement' => 'span',
                'classList' => [
                    'mod-menu__child-label'
                ]
            ])
                {{ $child['lastWord'] }}
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
    @endelement
@endlink