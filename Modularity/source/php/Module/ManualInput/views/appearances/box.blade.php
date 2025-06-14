@element([
    'attributeList' => $input['attributeList'] ?? [],
    'classList'     => array_merge($input['classList'] ?? [], [$input['columnSize']])
])
    @box([
        'heading'   => $input['title'],
        'content'   => $input['content'],
        'link'      => $input['link'],
        'ratio'     => $ratio,
        'image'     => $input['image'],
        'icon'      => $input['icon'],
        'context'   => $context
    ])
    @endbox
@endelement