<div class="o-container s-archive s-archive-secondary s-archive-template-{{ sanitize_title($secondaryTemplate) }}">

    {!! $hook->secondaryLoopStart !!}

    @includeFirst([
        'partials.archive.archive-filters-' . $secondaryPostType . '-secondary',
        'partials.archive.archive-filters-secondary',
    ])
    @if (!empty($secondaryQuery->posts))

        @if ($displaySecondaryMap && !empty($secondaryQuery->pins))

            @openStreetMap([
                'classList' => ['u-margin__top--4'],
                'height' => '100vh',
                'containerAware' => true,
                'fullWidth' => true
            ])
                @slot('sidebarContent')
                    @includeIf('partials.sidebar', [
                        'id' => 'right-sidebar',
                        'classes' => ['o-grid', 'openstreetmap-right-sidebar'],
                    ])
                    @select([
                        'label' => $lang->sortBy,
                        'hidePlaceholder' => true,
                        'required' => true,
                        'preselected' => 'random',
                        'size' => 'sm',
                        'limitWidth' => true,
                        'options' => [
                            'default' => $lang->sortPublishDate,
                            'alphabetical' => $lang->sortName,
                            'random' => $lang->sortRandom,
                        ],
                        'attributeList' => [
                            'data-js-pagination-sort' => '',
                        ],
                        'classList' => [
                            'u-margin__bottom--4',
                            'u-margin__left--auto',
                        ],
                    ])
                    @endselect

                    @collection([
                        'classList' => ['o-grid', 'o-grid--horizontal', 'u-margin__top--0'],
                        'attributeList' => [
                            'data-js-pagination-container' => ''
                        ]
                    ])
                        @foreach ($secondaryQuery->posts as $place)
                            <div class="c-openstreetmap__posts" data-js-pagination-item tabindex="0" data-js-pagination-item-title="{{$place->postTitle}}">
                                @collection__item([
                                    'classList' => ['c-openstreetmap__collection__item'],
                                    'containerAware' => true,
                                    'bordered' => true,
                                    'attributeList' => [
                                        'data-js-map-location' => json_encode(!empty($place->location) ? $place->location['pin'] : []),
                                    ]
                                ])
                                    @if (!empty($place->callToActionItems['floating']))
                                        @slot('floating')
                                            @icon($place->callToActionItems['floating'])
                                            @endicon
                                        @endslot
                                    @endif
                                    @slot('before')
                                        @if (!empty($place->images['thumbnail1:1']['src']))
                                            @image([
                                                'src' => $place->images['thumbnail3:4']['src'],
                                                'alt' => $place->images['thumbnail1:1']['alt'] ? $place->images['thumbnail1:1']['alt'] : $place->postTitle,
                                                'classList' => ['u-width--100']
                                            ])
                                            @endimage
                                        @endif
                                    @endslot
                                    @group([
                                        'direction' => 'vertical'
                                    ])
                                        @group([
                                            'justifyContent' => 'space-between',
                                            'alignItems' => 'flex-start'
                                        ])
                                            @typography([
                                                'element' => 'h2',
                                                'variant' => 'h3',
                                            ])
                                                {{ $place->postTitle }}
                                            @endtypography
                                        @endgroup
                                        @tags([
                                            'tags' => $place->termsUnlinked,
                                            'classList' => ['u-padding__y--2']
                                        ])
                                        @endtags
                                    @endgroup
                                @endcollection__item

                                {{-- Post (full content) --}}
                                @group([
                                    'classList' => ['c-openstreetmap__post'],
                                    'containerAware' => true
                                ])
                                    @icon([
                                        'icon' => 'arrow_back',
                                        'size' => 'md',
                                        'color' => 'white',
                                        'classList' => ['c-openstreetmap__post-icon']
                                    ])
                                    @endicon
                                    @if (!empty($place->images['featuredImage']['src']))
                                        @hero([
                                            'image' => $place->images['featuredImage']['src']
                                        ])
                                        @endhero
                                    @endif
                                    <div class="u-margin__x--2">
                                        @paper([
                                            'attributeList' => [
                                                'style' => !empty($place->images['featuredImage']['src'])
                                                    ? 'transform:translateY(calc(max(-50%, -50px)))'
                                                    : 'margin-top: 32px'
                                            ],
                                            'containerAware' => true,
                                            'classList' => ['u-padding--6', 'o-container']
                                        ])
                                            @group([
                                                'justifyContent' => 'space-between',
                                                'alignItems' => 'flex-start'
                                            ])
                                                @typography([
                                                    'element' => 'h2',
                                                    'variant' => 'h1'
                                                ])
                                                    {{ $place->postTitle }}
                                                @endtypography
                                                @if(is_array($place->callToActionItems['floating']))
                                                    @icon($place->callToActionItems['floating'])
                                                    @endicon
                                                @endif
                                            @endgroup
                                            <div class="o-grid c-openstreetmap__post-container">
                                                <div class="c-openstreetmap__post-content">
                                                    @typography([])
                                                        {!! $place->postContentFiltered !!}
                                                    @endtypography
                                                </div>
                                                @if (!empty($place->placeInfo))
                                                    <div class="c-openstreetmap__post-list">
                                                        @listing([
                                                            'list' => $place->placeInfo,
                                                            'icon' => false,
                                                            'classList' => ['unlist'],
                                                            'padding' => 4
                                                        ])
                                                        @endlisting
                                                    </div>
                                                @endif
                                                @if (!empty($place->bookingLink))
                                                    @button([
                                                        'classList' => ['c-openstreetmap__post-button'],
                                                        'text' => $lang->bookHere ?? 'Book here',
                                                        'color' => 'primary',
                                                        'style' => 'filled',
                                                        'href' => $place->bookingLink,
                                                    ])
                                                    @endbutton
                                                @endif
                                            </div>
                                        @endpaper
                                    </div>
                                @endgroup
                            </div>
                        @endforeach
                    @endcollection

                    @pagination([
                        'classList' => ['u-padding__top--8', 'u-padding__bottom--6', 'u-justify-content--center'],
                        'useJS' => true,
                        'current' => 1,
                        'perPage' => 20,
                        'pagesToShow' => 4,
                        'keepDOM' => true,
                    ])
                    @endpagination
                @endslot
            @endopenStreetMap
        @else
            @includeIf("partials.post.post-{$secondaryTemplate}", ['posts' => $secondaryQuery->posts])
        @endif
        @if ($showSecondaryPagination)
            @pagination([
                'list' => $secondaryPaginationList,
                'classList' => ['u-margin__top--8', 'u-display--flex', 'u-justify-content--center'],
                'current' => $currentPage,
                'linkPrefix' => "?$secondaryPaginationLinkPrefix",
                'anchorTag' => '#filter'
            ])
            @endpagination
        @endif
    @else
        <div class="o-grid">
            <div class="o-grid-12">
                @notice([
                    'type' => 'info',
                    'message' => [
                        'text' => $lang->noResult,
                        'size' => 'md'
                    ]
                ])
                @endnotice
            </div>
        </div>
    @endif

    {!! $hook->secondaryLoopEnd !!}

</div>
