@if (empty($hideTitle) && !empty($postTitle))
    <div class="c-card__header">
        @include('partials.post-title', ['variant' => 'h4', 'classList' => []])
    </div>
@endif

@includeWhen(!empty($freeTextFiltering), 'partials.search-field')

@accordion([
    'heading' => $accordionColumnTitles,
    'spacing' => $accordionSpacedSections,
    'divider' => $divider ?? false
])
    @foreach ($manualInputs as $input)
        @accordion__item([
            'heading' => $input['accordionColumnValues'],
            'attributeList' => array_merge([
                'js-filter-item' => '',
                'js-filter-data' => ''
            ],
                $input['attributeList'] ?? []),
            'classList' => $input['classList'] ?? []
        ])
            {!! $input['content'] !!}
        @endaccordion__item
    @endforeach
@endaccordion