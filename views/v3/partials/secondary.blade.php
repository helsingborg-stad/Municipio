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
                'height' => '100vh',
                'containerAware' => true,
                'fullWidth' => true
            ])
                @slot('sidebarContent')
                    @includeIf('partials.sidebar', [
                        'id' => 'right-sidebar',
                        'classes' => ['o-grid', 'openstreetmap-right-sidebar'],
                    ])

                    @collection([
                        'classList' => ['o-grid', 'o-grid--horizontal'],
                        'attributeList' => [
                            'js-pagination-container' => ''
                        ]
                    ])
                        @foreach ($secondaryQuery->posts as $place)
                            <div class="c-openstreetmap__posts" js-pagination-item>
                                @collection__item([
                                    'classList' => ['c-openstreetmap__collection__item'],
                                    'containerAware' => true,
                                    'bordered' => true,
                                    'attributeList' => [
                                        'js-map-lat' => $place->location['lat'],
                                        'js-map-lng' => $place->location['lng'],
                                        'js-data-url' => $place->permalink
                                    ]
                                ])
                                    @if ($place->callToActionItems['floating'])
                                        @slot('floating')
                                            @icon($place->callToActionItems['floating'])
                                            @endicon
                                        @endslot
                                    @endif
                                    @slot('before')
                                        @if (!empty($place->thumbnail['src']))
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
                                            'alignItems' => 'flex-start'
                                        ])
                                            @typography([
                                                'element' => 'h2',
                                                'variant' => 'h3'
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
                                    'classList' => ['c-openstreetmap__post', 'u-margin__top--0'],
                                    'containerAware' => true
                                ])
                                    @icon([
                                        'icon' => 'arrow_back',
                                        'size' => 'md',
                                        'color' => 'white',
                                        'classList' => ['c-openstreetmap__post-icon']
                                    ])
                                    @endicon
                                    @if (!empty($place->featuredImage['src']))
                                        @hero([
                                            'image' => $place->featuredImage['src']
                                        ])
                                        @endhero
                                    @endif
                                    <div class="u-margin__x--2">
                                        @paper([
                                            'attributeList' => [
                                                'style' => !empty($place->featuredImage['src'])
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
                                                @icon($place->callToActionItems['floating'])
                                                @endicon
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
                        'list' => [['href' => '?pagination=1', 'label' => 'Page 1']],
                        'classList' => ['u-padding__top--8', 'u-padding__bottom--6', 'u-justify-content--center'],
                        'useJS' => true,
                        'current' => 1,
                        'perPage' => 20,
                        'pagesToShow' => 4
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
