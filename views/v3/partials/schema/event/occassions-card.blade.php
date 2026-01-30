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
                    {!! $currentOccasion->getStartDate() !!} - {!! $currentOccasion->getEndTime() !!}
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
                                    {!! $currentOccasion->getStartDate() !!} - {!! $currentOccasion->getEndTime() !!}
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