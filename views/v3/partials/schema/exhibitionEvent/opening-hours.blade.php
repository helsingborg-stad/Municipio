@collection()
    @collection__item([])
        @typography(['element' => 'h4', 'variant' => 'h4'])
            {!! $lang->openingHoursLabel !!}
        @endtypography
        @typography([])
            {!! $openingHours !!}
        @endtypography
    @endcollection__item
@endcollection