@collection()
    @collection__item([])
        @typography(['element' => 'h4', 'variant' => 'h4'])
            {!! $lang->specialOpeningHoursLabel !!}
        @endtypography
        @typography([])
            @foreach ($specialOpeningHours as $line)
                {!! $line !!}
            @endforeach
        @endtypography
    @endcollection__item
@endcollection