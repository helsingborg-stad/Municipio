@element([
    'attributeList' => $input['attributeList'] ?? [],
    'classList'     => array_merge($input['classList'] ?? [], [$input['columnSize']])
])
    @card([
        'link'              => $input['link'],
        'heading'           => $input['title'],
        'context'           => $context,
        'content'           => $input['content'],
        'image'             => $input['image'],
        'containerAware'    => true,
        'classList'         => array_merge($input['classList'] ?? [], ['u-height--100']),
        'hasPlaceholder'    => $anyItemHasImage,
        'icon'              => $input['icon'] ? [
            'icon' => $input['icon'],
            'size' => 'md',
            'color' => 'black'
        ] : null
    ])
    @endcard
@endelement