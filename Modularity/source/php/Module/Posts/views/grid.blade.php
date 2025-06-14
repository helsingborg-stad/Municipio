@includeWhen((!$hideTitle && !empty($postTitle))|| !empty($titleCTA), 'partials.post-title',
    ['titleCTA' => $titleCTA ?? null]
)
@includeWhen($preamble, 'partials.preamble')
<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}{{ !empty($noGutter) ? ' o-grid--no-gutter' : '' }}{{ (!empty($preamble)||(!$hideTitle && !empty($postTitle))) ? ' u-margin__top--4' : '' }}"
    @if (!$hideTitle && !empty($postTitle)) aria-labelledby="{{ 'mod-posts-' . $ID . '-label' }}" @endif>
    @if($posts)
        @foreach ($posts as $post)
            <div class="{{!empty($post->classList) ? implode(' ', $post->classList) : ''}}" 
            {{!empty($post->attributeList) ? implode(' ', array_map(function($key, $value) {
                return $key . '=' . $value;
            }, array_keys($post->attributeList), $post->attributeList)) : '' }}>
                @if ($post->isHighlighted && $highlight_first_column_as === 'card')
                    @include('partials.post.card')
                @else
                    @include('partials.post.block')
                @endif
            </div>
        @endforeach
    @endif
</div>

@include('partials.more')
