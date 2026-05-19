@element([
    'componentElement' => 'article',
    'classList' => [
        'c-article',
    ],
    'attributeList' => [
        ...(!$hideTitle && !empty($postTitle) ? [
            'aria-labelledby' => 'mod-text-' . $ID . '-label'] : []
        ),
    ]
])
    @includeWhen(!$hideTitle && !empty($postTitle), 'partials.postTitle', ['variant' => 'h2'])

    @if($postContent)
        {!! $postContent !!}
    @endif
@endelement
