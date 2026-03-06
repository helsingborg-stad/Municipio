@paper(['padding' => 2])
    @collection()
        @foreach ($informationList as $item)
            @collection__item([])
                @typography(['element' => 'h2', 'variant' => 'h3'])
                    {{$item['label']}}
                @endtypography
                @if(is_array($item['value']))
                    @foreach ($item['value'] as $value)
                        @if(!empty($value))
                            @typography(){!!$value!!}@endtypography
                        @endif
                    @endforeach
                @else
                    @typography(){{$item['value']}}@endtypography
                @endif
            @endcollection__item
        @endforeach
    @endcollection
@endpaper
