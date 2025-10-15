@typography([
    'element' => 'span',
    'classList' => [
        'u-display--flex',
        'u-align-items--center',
        'u-font-size--meta'
    ]
])
    @icon([
        'icon' => 'timer',
        'size' => 'sm',
        'attributeList' => [
            'style' => 'margin-right: 4px;',
        ],
    ]) 
    @endicon {{ $post->readingTime }}
@endtypography