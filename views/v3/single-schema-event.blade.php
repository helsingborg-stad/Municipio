@extends('templates.master')

@section('site-wrapper')
    {{-- Notices as banner style --}}
    @if ($notice && $notice['banner'])
        @foreach ($notice['banner'] as $noticeItem)
            @notice($noticeItem)
            @endnotice
        @endforeach
    @endif

    {{-- Site banner --}}
    @section('site-banner')
        @includeIf('partials.sidebar', ['id' => 'header-area-site-banner', 'classes' => []])
    @show

    {{-- Site header --}}
    @section('site-header')
        @if (!empty($customizer->headerApperance))
            @includeIf('partials.header.' . $customizer->headerApperance)
        @endif
    @show

    @includeWhen(!$helperNavBeforeContent, 'partials.navigation.helper', [
        'classList' => ['screen-reader-text'],
    ])

    {{-- Hero area and top sidebar --}}
    @section('hero-top-sidebar')
        @includeIf('partials.hero', ['classes' => []])
        @includeIf('partials.sidebar', ['id' => 'top-sidebar'])
    @show

    {{-- Before page layout --}}
    @section('before-layout')
    @show

    {{-- Notices before content --}}
    @if ($notice && $notice['content'])
        @element([
            'classList' => [
                'o-container',
                'u-margin__top--4',
                'u-margin__bottom--4'
            ]
        ])
            @foreach ($notice['content'] as $noticeItem)
                @notice($noticeItem)
                @endnotice
            @endforeach
        @endelement
    @endif

    @if($eventIsInThePast)
        @element([
            'classList' => [
                'o-container',
                'u-margin__top--4',
                'u-margin__bottom--4'
            ]
        ])
            @notice([
                'type' => 'warning',
                'message' => [
                    'text' => $lang->expiredEventNotice,
                ],
                'icon' => [
                    'icon' => 'schedule'
                ]
            ])@endnotice
        @endelement
    @endif
    @section('layout')
        @element([
            'id' => 'main-content',
            'classList' => [
                'o-container',
                'o-layout-grid',
                'o-layout-grid--cols-12',
                'o-layout-grid--column-gap-8',
                'o-layout-grid--row-gap-12',
                'u-margin__y--8'
            ]
        ])
            @section('above')
                @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => ['o-layout-grid--col-span-12']])
            @stop

            @yield('above')

            @element([
                'classList' => [
                    'o-layout-grid--col-span-12',
                    'o-layout-grid',
                    'o-layout-grid--cols-12',
                    'o-layout-grid--gap-4',
                    'o-layout-grid--order-0',
                    'u-padding__x--8',
                    'u-padding__y--8',
                    'u-rounded--16',
                ],
                'attributeList' => [
                    'style' => 'background-color: color-mix(in srgb, var(--color-secondary), transparent 70%)'
                ]
            ])
                @element([
                    'classList' => [
                        'o-layout-grid--col-span-11'
                    ],
                    'attributeList' => [
                        'style' => 'max-width: 800px; color: var(--color-secondary-contrasting);'
                    ]
                ])
                    @typography(['element' => 'h1', 'variant' => 'h1'])
                        {!! $post->getTitle() !!}
                    @endtypography
                    @if(!empty($description))
                    {{-- TODO: Fix this to the correct content --}}
                        @typography(['element' => 'p', 'variant' => 'subtitle'])
                            {!! $description !!}
                        @endtypography
                    @endif
                @endelement
                @element([
                    'classList' => [
                        'o-layout-grid--justify-end'
                    ]
                ])
                    @datebadge([
                        'date' => $currentOccasion->getStartDate(),
                        'translucent' => true
                    ])
                    @enddatebadge
                @endelement
            @endelement

            @section('content')
                @section('sidebar-left')
                    @include('partials.sidebar', [
                        'id' => 'left-sidebar',
                        'classes' => [
                            'o-layout-grid',
                            'o-layout-grid--gap-6'
                        ],
                    ])

                    @include('partials.sidebar', [
                        'id' => 'left-sidebar-bottom',
                        'classes' => [
                            'o-layout-grid',
                            'o-layout-grid--gap-6'
                        ],
                    ])
                @stop
                @section('sidebar-right')
                        @card([
                            'heading' => $lang->placeTitle,
                            'content' => $place['address']
                        ])
                            @if(!empty($place['url']))
                                @slot('belowContent')
                                        @link(['href' => $place['url']])
                                            {{$lang->directionsLabel}}
                                        @endlink
                                @endslot
                            @endif
                        @endcard

                        @card([
                            'heading' => $lang->occasionsTitle
                        ])
                            @slot('aboveContent')
                                @if(!empty($currentOccasion))
                                    @typography([
                                        'classList' => [
                                            'u-display--flex',
                                            'u-align-items--center',
                                            'o-layout-grid--gap-2'
                                        ]

                                    ])
                                        @icon([
                                            'icon' => 'calendar_month',
                                            'size' => 'lg'
                                        ])
                                        @endicon
                                        @typography([
                                            'element' => 'span',
                                            'classList' => [
                                                'u-bold'
                                            ],
                                            'attributeList' => [
                                                'style' => 'margin-top: 3px;'
                                            ]
                                        ])
                                            {!! $currentOccasion->getStartDate() !!}
                                        @endtypography
                                    @endtypography
                                @endif

                                @if(!empty($occasions) && count($occasions) > 1)
                                    @accordion([])
                                        @accordion__item([
                                            'heading' => $lang->moreOccasions
                                        ])
                                            @collection([
                                                'compact' => true
                                            ])
                                            @foreach($occasions as $occasion)
                                               @if(!$occasion->isCurrent())
                                                @collection__item([
                                                    'link' => $occasion->getUrl(),
                                                    'icon' => 'chevron_forward',
                                                    'iconLast' => true
                                                ])
                                                @typography([
                                                            'element' => 'span',
                                                        ])
                                                            {!! $occasion->getStartDate() !!}
                                                        @endtypography
                                                @endcollection__item
                                                @endif
                                            @endforeach
                                            @endcollection
                                        @endaccordion__item
                                    @endaccordion
                                @endif
                            @endslot
                        @endcard
                        @if(!empty($bookingLink) && !$eventIsInThePast)
                            @card([
                                'heading' => $lang->bookingTitle
                            ])
                                @slot('aboveContent')
                                    @button([
                                        'href' => $bookingLink,
                                        'color' => 'primary',
                                        'style' => 'filled',
                                        'size' => 'md',
                                        'icon' => 'open_in_new',
                                        'fullWidth' => false,
                                        'text' => $lang->bookingButton,
                                        'classList' => [
                                            'u-margin__top--2'
                                        ],
                                        'attributeList' => [
                                            'style' => 'justify-self: start;',
                                        ],
                                        'target' => '_blank'
                                    ])
                                    @endbutton
                                    @typography([
                                        'element' => 'span',
                                        'variant' => 'meta'
                                    ])
                                        {!! $lang->bookingDisclaimer !!}
                                    @endtypography
                                @endslot
                            @endcard
                        @endif

                        @includeIf('partials.sidebar', ['id' => 'right-sidebar', 'classes' => [
                            'o-layout-grid',
                            'o-layout-grid--gap-6'
                        ]])
                @stop
                @php
                    $leftSidebarHasContent = !empty(trim($__env->yieldContent('sidebar-left')));
                    $rightSidebarHasContent = !empty(trim($__env->yieldContent('sidebar-right')));
                    $mainColumnSize = 12;

                    if ($leftSidebarHasContent && $rightSidebarHasContent) {
                        $mainColumnSize = 6;
                    } elseif ($leftSidebarHasContent || $rightSidebarHasContent) {
                        $mainColumnSize = 8;
                    }
                @endphp

                @hasSection('sidebar-left')
                    @element([
                        'classList' => [
                            'o-layout-grid',
                            'o-layout-grid--col-span-' . ($rightSidebarHasContent ? 3 : 4) . '@md',
                            'o-layout-grid--col-span-12',
                            'o-layout-grid--gap-6',
                            'u-print-display--none',
                            'o-layout-grid--grid-auto-rows-min-content',
                            'o-layout-grid--order-1@md',
                            'o-layout-grid--order-2'
                        ]
                    ])
                        @yield('sidebar-left')
                    @endelement
                @endif
                @element([
                    'classList' => [
                        'o-layout-grid--col-span-' . $mainColumnSize . '@md',
                        'o-layout-grid--col-span-12',
                        'o-layout-grid',
                        'o-layout-grid--gap-6',
                        'o-layout-grid--grid-auto-rows-min-content',
                        'o-layout-grid--order-2@md',
                        'o-layout-grid--order-1'
                    ]
                ])
                    @image([
                        'src' => $post->getImage(),
                        'rounded' => 'lg',
                        'classList' => [
                            'u-aspect-ratio--16-9',
                            'u-width--100'
                        ]
                    ])
                    @endimage
                    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => []])

                    @element([])
                        {!! $post->getContent() !!}
                    @endelement

                    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => []])

                    @if(!empty($accessibilityFeatures))
                        @card([
                            'heading' => $lang->accessibilityTitle
                        ])
                            @slot('aboveContent')
                                @element(['componentElement' => 'ul'])
                                    @foreach($accessibilityFeatures as $feature)
                                        @element(['componentElement' => 'li']){!! $feature !!}@endelement
                                    @endforeach
                                @endelement
                            @endslot
                        @endcard
                    @endif
                @endelement
                @hasSection('sidebar-right')
                    @element([
                        'classList' => [
                            'o-layout-grid',
                            'o-layout-grid--gap-6',
                            'o-layout-grid--col-span-' . ($leftSidebarHasContent ? 3 : 4) . '@md',
                            'o-layout-grid--col-span-12',
                            'u-print-display--none',
                            'o-layout-grid--grid-auto-rows-min-content',
                            'o-layout-grid--order-3',
                        ],
                    ])
                        @yield('sidebar-right')
                    @endelement
                @endif

            @stop
            @yield('content')
            
            @element([
                'classList' => [
                    'o-layout-grid--col-span-12',
                    'o-layout-grid',
                    'o-layout-grid--gap-6',
                    'o-layout-grid--order-23',
                    'o-layout-grid--cols-12',
                    'u-padding__x--8',
                    'u-padding__y--8',
                    'u-rounded--16',
                ],
                'attributeList' => [
                    'style' => 'color: var(--color-secondary-contrasting); background-color: color-mix(in srgb, var(--color-secondary), transparent 70%)'
                ]
            ])
                @typography([
                    'element' => 'h2',
                    'variant' => 'h2',
                    'classList' => [
                        'o-layout-grid--col-span-12'
                    ]
                ])
                    {!! $lang->relatedEventsTitle !!}
                @endtypography
                @foreach($relatedPosts as $relatedPost)
                    @card([
                        'image' => $relatedPost->getImage(),
                        'heading' => $relatedPost->getTitle(),
                        'link' => $relatedPost->getPermalink(),
                        'dateBadge' => true,
                        'date' => [
                            'timestamp' => $relatedPost->getArchiveDateTimestamp(),
                            'format'    => $relatedPost->getArchiveDateFormat(),
                        ],
                        'classList' => [
                            'o-layout-grid--col-span-12',
                            'o-layout-grid--col-span-6@md',
                            'o-layout-grid--col-span-4@lg',
                        ]
                    ])
                    @endcard
                @endforeach
            @endelement

            @section('below')
                @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
            @stop

            @hasSection('below')
                @element([
                    'classList' => [
                        'o-layout-grid--col-span-12',
                        'o-layout-grid--order-24'
                    ]
                ])
                    @yield('below')
                @endelement
            @endif
        @endelement
    @show
@stop