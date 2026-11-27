@card([])
    @card__body([])
        {{-- Same heading markup as cards using the heading attribute, e.g. place and organizers --}}
        @typography([
            'element' => 'h2',
            'variant' => 'h3',
            'classList' => ['c-card__heading', 'u-margin__y--0']
        ])
            {{$lang->occasionsTitle}}
        @endtypography

        @if(!empty($currentOccasion))
            @typography([
                'classList' => [
                    'u-display--flex',
                    'u-align-items--center',
                    'o-layout-grid--gap-2',
                    'u-margin__top--1'
                ],
                'attributeList' => [
                    'style' => 'margin-left: -3px;'
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
                    {!! $currentOccasion->getStartDate() !!} - {!! $currentOccasion->getEndTime() !!}
                @endtypography
            @endtypography
        @endif
    @endcard__body

    @if(!empty($occasions) && count($occasions) > 1)
        @accordion([])
            @accordion__item([
                'heading' => $lang->moreOccasions,
                'attributeList' => [
                    'style' => '--c-accordion--inset-padding-x: 0;'
                ]
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
                                {!! $occasion->getStartDate() !!} - {!! $occasion->getEndTime() !!}
                            @endtypography
                    @endcollection__item
                    @endif
                @endforeach
                @endcollection
            @endaccordion__item
        @endaccordion
    @endif
@endcard