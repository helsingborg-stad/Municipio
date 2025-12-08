@if (empty($hideTitle) && !empty($postTitle) && $accordionSpacedSections)
    <div class="u-margin__bottom--2">
        @include('partials.post-title', ['variant' => 'h2', 'classList' => []])
    </div>
@endif
<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}" {{!empty($freeTextFiltering) ? 'js-filter-container=' . $ID : ''}}>
    @card([
        'context' => $context
    ])
        @if (empty($hideTitle) && !empty($postTitle) && !$accordionSpacedSections)
            <div class="c-card__header">
                @include('partials.post-title', ['variant' => 'h4', 'classList' => []])
            </div>
        @endif
        <div>
            @includeWhen(!empty($freeTextFiltering), 'partials.search-field')
            @if (!empty($accordionColumnTitles))
                <header class="accordion-table__head">
                    @foreach ($accordionColumnTitles as $title)
                        @typography([
                            'element' => 'span',
                            'classList' => ['accordion-table__head-column']
                        ])
                            {{ $title }}
                        @endtypography
                    @endforeach
                </header>
            @endif
    
            @accordion([
                'spacedSections' => $accordionSpacedSections
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
    @endcard
</div>
