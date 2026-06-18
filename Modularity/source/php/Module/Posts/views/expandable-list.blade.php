<div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}">
@card([      
    'heading' => false,
    'attributeList' => [
        'js-filter-container' => $ID,
        ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-posts-' . $ID . '-label'] : []),
    ],
    'context' => 'module.posts.expandablelist'
])
    @if ((!$hideTitle && !empty($postTitle)) || !empty($titleCTA))
        @element([
            'classList' => ['c-card__header']
        ])
            @include('partials.post-title', ['variant' => 'h4', 'classList' => [], 'titleCTA' => $titleCTA ?? null])
        @endelement
    @endif
    @if (!isset($allow_freetext_filtering) || $allow_freetext_filtering)
        @element([
            'classList' => ['c-card__body'],
            'attributeList' => [
                'aria-label' => __('Search', 'municipio')
            ]
        ])
            @field([
                'type' => 'search',
                'name' => 'search',
                'label' => __('Search', 'municipio'),
                'hideLabel' => true,
                'attributeList' => [
                    'js-filter-input' => $ID
                ],
                'placeholder' => __('Search', 'municipio')
            ])
            @endfield
        @endelement
    @endif
    @accordion([
        'heading' => $accordionHeadings ?? []
    ])
        @foreach($prepareAccordion as $accordionItem)
            @accordion__item([
                'heading' => $accordionItem['headings'],
                'attributeList' => array_merge(
                    [
                        'js-filter-item' => '',
                        'js-filter-data' => ''
                    ],
                    $accordionItem['attributeList']
                ),
            ])
                {!! $accordionItem['content'] !!}
            @endaccordion__item
        @endforeach
    @endaccordion
@endcard
</div>

@include('partials.more')
