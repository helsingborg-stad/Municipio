<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" href="{{ $href }}" {!! $attribute !!}>
    <div class="{{$baseClass}}__top">
        @avatar([
            'name' => $author,
            'icon' => [
                'name' => $icon,
                'size' => 'lg'
            ],
            'image' => $author_image
        ])
        @endavatar

        @if ($author_url)
            @link([
                'href' => $author_url,
                'classList' => ['c-comment__link']
            ])
        @endif
        
            @typography([
                "variant" => "title",
                "element" => "h6",
                "classList" => [$baseClass.'__author']
            ])
                {{$author}}
            @endtypography

        @if ($author_url)
            @endlink
        @endif
        

        @if ($date)
            @typography([
                "variant" => "meta",
                "element" => "p",
                "classList" => [$baseClass.'__date']
            ])
                @date([
                    'action' => 'timesince',
                    'timestamp' => $date
                ])
                @enddate
            @endtypography
        @endif
    </div>

    <div class="{{$baseClass}}__bubble">
        @typography([
            "variant" => "body",
            "element" => "p"
        ])
            {{$text ? $text : $slot}}
        @endtypography
    </div>
</{{$componentElement}}>