@element([
    'componentElement' => ((!$hideTitle && !empty($postTitle)) || $hasHeadingsInContent) ? 'article' : 'div',
    'attributeList' => [
        ...(!empty($font_size) ? ['class' => $font_size] : []),
        ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-text-' . $ID . '-label'] : []),
    ]
])
    @includeWhen(!$hideTitle && !empty($postTitle), 'partials.postTitle', ['variant' => 'h2'])

    @if($postContent)
        {!! $postContent !!}
    @endif
@endelement