@element()
    @if ($lang->eventsLabel)
        @typography(['element' => 'h2', 'classList' => ['u-margin__bottom--2']])
            {{ $lang->eventsLabel }}
        @endtypography
    @endif
    @foreach ($events as $event)
        @collection([])
            @collection__item([
                'classList' => ['u-box-shadow--3', 'u-margin__bottom--3', 'u-padding--1', 'u-border--1']
            ])
                @slot('before')
                    @datebadge([
                        'classList' => ['u-padding--2', 'u-margin__right--2'],
                        'date' => $event['timestamp'],
                        'size' => 'sm'
                    ])
                    @enddatebadge
                @endslot
                    {!! $event['name'] !!}
                @typography(['classList' => ['u-padding__bottom--2', 'u-padding__top-2']])
                    {!! $event['startTimeEndTime'] !!}
                @endtypography
                {!! $event['description'] !!}
            @endcollection__item
        @endcollection
    @endforeach
@endelement