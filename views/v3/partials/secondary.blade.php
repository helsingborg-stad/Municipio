<div class="o-container s-archive s-archive-secondary s-archive-template-{{ sanitize_title($secondaryTemplate) }}">

    {!! $hook->secondaryLoopStart !!}

    @includeFirst([
        'partials.archive.archive-filters-' . $secondaryPostType . '-secondary',
        'partials.archive.archive-filters-secondary',
    ])
    @if (!empty($secondaryQuery->posts))

        @if ($displaySecondaryMap && !empty($secondaryQuery->pins))
            @openStreetMap([
                'pins' => $secondaryQuery->pins,
                'classList' => ['u-margin__top--4'],
                'startPosition' => ['lat' => '56.046029', 'lng' => '12.693904', 'zoom' => 14],
                'height' => '100vh',
                'containerAware' => true,
                'mapStyle' => 'default',
                'fullWidth' => true,
            ])
            @slot('sidebarContent')
                @collection([
                    'classList' => ['o-grid', 'o-grid--horizontal'],
                    'attributeList' => [
                        'js-pagination-container' => '',
                    ]
                ])
                @foreach($secondaryQuery->posts as $place)
                    <div class="c-openstreetmap__posts" js-pagination-item>
                        @collection__item([
                            'classList' => ['c-openstreetmap__collection__item'],
                            'containerAware' => true,
                            'bordered' => true,
                            'attributeList' => [
                                'js-map-lat' => $place->location['lat'], 
                                'js-map-lng' => $place->location['lng'],
                            ]
                        ])
                        @slot('before')
                            @if(!empty($place->thumbnail['src']))
                                @image([
                                    'src' => $place->thumbnail['src'],
                                    'alt' => $place->thumbnail['alt'] ? $place->thumbnail['alt'] : $place->postTitle,
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
                                'alignItems' => 'flex-start',
                            ])
                                @typography([
                                    'element' => 'h2',
                                    'variant' => 'h3',
                                ])
                                    {{$place->postTitle}}
                                @endtypography
                                @if($place->termIcon['icon'])
                                    @inlineCssWrapper([
                                        'styles' => ['background-color' => $place->termIcon['backgroundColor'], 'display' => 'flex'],
                                        'classList' => [$place->termIcon['backgroundColor'] ? '' : 'u-color__bg--primary', 'u-rounded--full', 'u-detail-shadow-3']
                                    ])
                                        @icon($place->termIcon)
                                        @endicon
                                    @endinlineCssWrapper
                                @endif
                            @endgroup
                                @tags([
                                    'tags' => $place->termsUnlinked,
                                    'classList' => ['u-padding__y--2'],
                                    'format' => true,
                                ])
                                @endtags
                                @typography([])
                                    {{$place->postExcerpt}}
                                @endtypography
                            @endgroup
                        @endcollection__item

                        {{-- Post (full content) --}}
                            @group([
                                'classList' => ['c-openstreetmap__post'],
                                'containerAware' => true,
                            ])
                            @icon([
                                'icon' => 'arrow_back',
                                'size' => 'md',
                                'color' => 'white',
                                'classList' => ['c-openstreetmap__post-icon'],
                            ])
                            @endicon
                            @if (!empty($place->thumbnail['src']))
                                @hero([
                                    'image' => $place->thumbnail['src']
                                ])
                                @endhero
                            @endif
                            <div class="u-margin__x--2">
                                @paper([
                                    'attributeList' => [
                                        'style' => !empty($place->thumbnail['src']) ? 'transform:translateY(calc(max(-50%, -50px)))' : 'margin-top: 32px'
                                    ],
                                    'containerAware' => true,
                                    'classList' => ['u-padding--6', 'o-container']
                                ])
                                @typography([
                                    'element' => 'h1',
                                    'variant' => 'h1'
                                    ])
                                    {{ $place->postTitle }}
                                @endtypography
                                    <div class="o-grid c-openstreetmap__post-container">
                                        <div class="c-openstreetmap__post-content">
                                                @typography([
                                                ])
                                                {!! $place->postContentFiltered !!}
                                                @endtypography
                                        </div>
                                        <div class="c-openstreetmap__post-list">
                                            @listing([
                                                'list' => $place->list,
                                                'icon' => false,
                                                'classList' => ['unlist'],
                                                'padding' => 4,
                                                ])
                                            @endlisting
                                        </div>
                                    </div>
                                @endpaper
                            </div>
                        @endgroup
                    </div>
                @endforeach
                @endcollection

                @pagination([
                    'list' => [
                        ['href' => '?pagination=1', 'label' => 'Page 1'],
                    ],
                    'classList' => [
                        'u-padding__top--8',
                        'u-padding__bottom--6',
                        'u-justify-content--center'
                    ],
                    'useJS' => true,
                    'current' => 1,
                    'perPage' => 20,
                    'pagesToShow' => 4,
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
