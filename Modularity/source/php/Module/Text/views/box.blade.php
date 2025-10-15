@card([
    'attributeList' => [
        ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-text-' . $ID . '-label'] : []),
    ],
    'context' => 'module.text.box'
])
    @if (empty($hideTitle) && !empty($postTitle))
        <div class="c-card__header">
            @include('partials.postTitle', ['variant' => 'h4'])
        </div>
    @endif
    
    @if($postContent)
        <div class="c-card__body">
            {!! $postContent !!}
        </div>
    @endif
@endcard