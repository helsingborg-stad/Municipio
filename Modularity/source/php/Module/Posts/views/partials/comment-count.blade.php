@typography([
    'element' => 'span',
    'classList' => [
        'u-display--flex',
        'u-align-items--center',
        'u-font-size--meta'
    ]
])
    @icon([
        'icon' => 'chat_bubble',
        'attributeList' => [
            'style' => 'margin-right: 4px;',
        ],
    ])
    @endicon
    {!! $post->commentCount !!}
@endtypography