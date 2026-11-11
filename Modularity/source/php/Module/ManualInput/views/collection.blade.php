@includeWhen(empty($hideTitle) && !empty($postTitle), 'partials.post-title')
@collection([
    'classList' => ['c-collection', 'o-grid', 'o-grid--horizontal', !empty($stretch) ? ' o-grid--stretch' : ''],
])
    @if (!empty($manualInputs))
        @foreach ($manualInputs as $input)
            @include('partials.collection-item', $input)
        @endforeach
    @endif
@endcollection
