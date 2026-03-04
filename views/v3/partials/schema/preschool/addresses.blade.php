@paper(['classList' => ['u-padding--2']])
    @element()
        @typography(['element' => 'h2'])
            {!! $lang->addressLabel !!}
        @endtypography
        @foreach ($addresses as $address)
            @if(!empty($address['address']))
                @typography()
                    {!! $address['address'] !!}
                @endtypography
            @endif
            @if(!empty($address['directionsLink']))
                @link(['href' => $address['directionsLink']['href']])
                    {!! $address['directionsLink']['label'] !!}
                @endlink
            @endif
        @endforeach

        @openStreetMap([
            ...$mapAttributes,
            'height' => '400px',
            'classList' => ['u-margin__top--2']
        ])
        @endopenStreetMap
    @endelement
@endpaper
