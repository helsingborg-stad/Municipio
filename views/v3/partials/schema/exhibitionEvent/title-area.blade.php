@typography([
    'element' => 'h1', 
    'variant' => 'h1', 
    'id' => 'page-title',
])
    {!! $post->getTitle() !!}
@endtypography

        @collection(['classList' => ['u-display--flex']])
            @collection__item([ 'icon' => 'event', ])
                @typography(['element' => 'h4', 'variant' => 'h2'])
                    {!! $lang->dateLabel !!}
                @endtypography
                @typography([])
                    {!! $occasion !!}
                @endtypography
            @endcollection__item
            @collection__item([ 'icon' => 'location_on' ])
                @typography(['element' => 'h4', 'variant' => 'h2'])
                    {!! $lang->placeTitle !!}
                @endtypography
                @typography([])
                    {!! $placeName !!}<br>
                    {!! $placeAddress !!}
                @endtypography
            @endcollection__item
        @endcollection