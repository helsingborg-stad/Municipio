@card([
    'context' => $context,
])
    @if ((empty($hideTitle) && !empty($postTitle)) || !empty($titleIcon))
    <div class="c-card__header">
        @include('partials.post-title', 
        [
            'variant' => 'h6',
            'titleIcon' => $titleIcon ?? null
        ])
    </div>
    @endif

    @if (!empty($manualInputs))
        @collection([
            'sharpTop' => true,
            'bordered' => true
        ])
            @foreach ($manualInputs as $input)
                @include('partials.list-item', $input)
            @endforeach
        @endcollection
    @endif
@endcard
