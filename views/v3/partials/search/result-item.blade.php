
@paper(['classList' => ['u-padding--2', 'u-margin__bottom--2']])


    @typography(['variant' => 'h3', 'element' => 'h3'])
        @link([
            'href' => $result['permalink'],
            'classList' => ['title-link']
        ])
            {{$result['title']}}
        @endlink
        
    @endtypography

    @typography([])
        {{$result['excerpt']}}
    @endtypography

    @typography(['variant' => 'meta'])
        @icon(['icon' => 'date_range', 'size' => 'sm'])
        @endicon
        
        {{$result['date']}}
    @endtypography

    @typography(['variant' => 'meta'])
        @icon(['icon' => 'link', 'size' => 'sm'])
        @endicon

        @link([
            'href' => $result['permalink']
        ])
            {{$result['permalink']}}
        @endlink

    @endtypography
    
@endpaper


