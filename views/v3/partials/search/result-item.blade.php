

@paper(['classList' => ['search-result-item', 'u-padding--2', 'u-margin__bottom--1']])
    @typography(['variant' => 'h2', 'element' => 'h2'])
        @link([
            'href' => $result['permalink'],
            'classList' => ['search-result-item__title-link']
        ])
            {{$result['title']}}
        @endlink
        
    @endtypography
 
    <p style="display: inline-block;">
        @if($result['featuredImage'])
            <img src="{{$result['featuredImage']}}">
        @endif
        {{$result['excerpt']}}
    </p>

    <div class="g-divider g-divider--lg"></div>

    @typography(['variant' => 'meta'])
        @icon(['icon' => 'date_range', 'size' => 'md'])
        @endicon
        
        <span class="">{{$result['date']}}</span>
    @endtypography

    @typography(['variant' => 'meta'])
        @icon(['icon' => 'link', 'size' => 'md'])
        @endicon

        @link([
            'href' => $result['permalink']
        ])
            {{$result['permalink']}}
        @endlink

    @endtypography
@endpaper    





