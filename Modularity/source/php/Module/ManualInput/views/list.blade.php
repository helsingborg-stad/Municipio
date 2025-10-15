@card([
    'context' => $context,
])
    @if ((empty($hideTitle) && !empty($postTitle)) || !empty($titleIcon))
    <div class="c-card__header">
        @include('partials.post-title', 
        [
            'variant' => 'h4', 'classList' => [],
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
                @collection__item([
                    'icon' => $input['icon'] ?? 'arrow_forward',
                    'link' => $input['link'],
                    'classList' => $input['classList'] ?? [],
                    'attributeList' => [
                        ...($input['attributeList'] ?? []),
                        ...($input['link'] ? ['aria-labelledby' => $input['id']] : [])
                    ]
                ])
                    @typography([
                        'element' => 'h2',
                        'variant' => 'h4',
                        'id'      => $input['id']
                    ])
                        {{ $input['title'] }}
                    @endtypography
                @endcollection__item
            @endforeach
        @endcollection
    @endif
@endcard