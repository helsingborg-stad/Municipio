@includeWhen((!$hideTitle && !empty($postTitle))|| !empty($titleCTA), 'partials.post-title',
    ['titleCTA' => $titleCTA ?? null]
)
@includeWhen($preamble, 'partials.preamble')
@collection([
    'classList' => ['c-collection', 'o-grid', 'o-grid--horizontal'],
])
    @if($posts)
        @foreach ($posts as $post)
            @include('partials.post.collection-item')
        @endforeach
    @endif
@endcollection

@include('partials.more')
