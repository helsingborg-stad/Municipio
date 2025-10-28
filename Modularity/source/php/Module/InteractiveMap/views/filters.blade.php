
@element([
    'classList' => [
        'interactive-map__filters-container',
        $filterDefaultOpen ? 'is-open' : ''
    ],
    'attributeList' => [
        'data-js-interactive-map-filters-container' => ''
    ]
])
@icon([
    'icon' => 'close',
    'size' => 'md',
    'classList' => [
        'interactive-map__filters-close-icon'
    ],
    'attributeList' => [
       'data-js-interactive-map-filters-close-icon' => '',
       'role' => 'button',
       'aria-label' => $lang['closeFilter']
    ]
])
@endicon
    @if (!empty($buttonFilters))
            @typography([
                'element' => 'h3',
                'variant' => 'h4',
                'classList' => [
                    'interactive-map__filters-button-title',
                    'u-margin__top--0'
                ],
            ])
                @if (empty($selectFilters) || count($selectFilters) <= 1)
                    {{$mainFilterTitle}}
                @else
                    {{$lang['filter']}}
                @endif
            @endtypography
        @foreach($buttonFilters as $level => $layerGroups)
            @element([
                'classList' => [
                    'interactive-map__filters-buttons'
                ],
                'attributeList' => [
                    'data-js-layer-group-level' => $level,
                ]
            ])
                @foreach($layerGroups as $layerGroup)
                    @button([
                        'text' => $layerGroup['title'] ?? 'Untitled',
                        'color' => 'primary',
                        'size' => 'sm',
                        'style' => 'outlined',
                        'toggle' => true,
                        'classList' => [
                            'interactive-map__filter-button',
                            ((empty($selectFilters) || count($selectFilters) > 1) && $level === 0) ? '' :'u-display--none'
                        ],
                        'attributeList' => [
                            'data-js-layer-group' => $layerGroup['id'] ?? '',
                        ],
                        'icon' => 'check_box_outline_blank',
                    ])
                    @endbutton
                @endforeach
            @endelement
        @endforeach
    @endif
    @if(!empty($selectFilters) && count($selectFilters) > 1)
        @element([
            'classList' => [
                'interactive-map__filters-selects'
            ]
        ])
            @typography([
                'element' => 'h3',
                'variant' => 'h4',
                'classList' => [
                    'interactive-map__filters-select-title'
                ],
            ])
                {{ $mainFilterTitle }}
            @endtypography
            @select([
                'options' => $selectFilters,
                'preselected' => $preselectedSelectFilter,
                'required' => true,
                'selectAttributeList' => [
                    'data-js-main-filter' => ''
                ],
                'classList' => [
                    'interactive-map__filter-select'
                ]
            ])
            @endselect
        @endelement
    @endif
@endelement