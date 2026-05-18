@element([
    'classList'     => $filterAboveCard ? ['has-filter-outside-card'] : [],
    'attributeList' => [
        'js-filter-container' => $uID
    ]
])
    @if ($isFilterable && $filterAboveCard)
        @element([
            'classList' => ['u-margin__bottom--2'],
        ])
            @include('partials.filter')
        @endelement
    @endif

    @card([
        'heading'       => false,
        'classList'     => [$classes],
        'attributeList' => [
            'js-filter-container' => $uID,
            ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-fileslist-' . $ID . '-label'] : []),
        ],
        'context'       => 'module.files.list'
    ])
        @if (!$hideTitle && !empty($postTitle))
            <div class="c-card__header"@if ($filterAboveCard) style="border-top-left-radius:0;border-top-right-radius:0;"@endif>
                @typography([
                    'id'      => 'mod-fileslist-' . $ID . '-label',
                    'element' => 'h2',
                    'variant' => 'h4',
                    'classList' => ['u-margin__y--0', 'u-font-size--subtitle-small']
                ])
                    {!! $postTitle !!}
                @endtypography
            </div>
        @endif

        @if ($isFilterable && !$filterAboveCard)
            @element([
                'classList' => ['u-padding--2'],
            ])
                @include('partials.filter')
            @endelement
        @endif

        @collection([
            'sharpTop' => true
        ])
            @foreach ($rows as $row)
                @collection__item([
                    'link'          => $row['href'],
                    'icon'          => $row['icon'],
                    'attributeList' => [
                        'js-filter-item' => ''
                    ]
                ])
                    @if ($showDownloadIcon)
                        @group([
                            'justifyContent' => 'space-between',
                            'classList' => ['u-gap-1', 'u-width--100']
                        ])
                            @group([
                                'classList' => ['u-display--block', 'u-width--100'],
                            ])
                                @include('partials.file')
                            @endgroup
                            @icon([
                                'icon' => 'download',
                                'size' => 'md'
                            ])
                            @endicon
                        @endgroup
                    @else
                        @include('partials.file')
                    @endif
                @endcollection__item
            @endforeach
        @endcollection
    @endcard
@endelement
