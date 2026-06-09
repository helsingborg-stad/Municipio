<div class="o-grid o-grid--half-gutter{{ !empty($stretch) ? ' o-grid--stretch' : '' }}" {{!empty($freeTextFiltering) ? 'js-filter-container=' . $ID : ''}}>
    @if (empty($hideTitle) && !empty($postTitle))
        <div class="c-card__header">
            @include('partials.post-title', ['variant' => 'h4', 'classList' => []])
        </div>
    @endif
    <div>
        @includeWhen(!empty($freeTextFiltering), 'partials.search-field')

        @accordion([
            'heading' => $accordionColumnTitles,
            'spacing' => $accordionSpacedSections
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
    </div>
</div>
