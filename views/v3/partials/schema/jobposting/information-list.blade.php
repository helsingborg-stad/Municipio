@element([
    'componentElement' => 'section',
])
    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']])
        {{$lang->information}}
    @endtypography

    @if(!empty($informationList))
        @paper(['padding' => 2])
            @collection()
                @foreach ($informationList as $item)
                    @collection__item([])
                        @typography(['element' => 'h4'])
                            {{$item['label']}}
                        @endtypography
                        @typography([])
                            {{$item['value']}}
                        @endtypography
                    @endcollection__item
                @endforeach
            @endcollection
        @endpaper
    @endif
@endelement
