<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" href="{{ $href }}" {!! $attribute !!}>
    <div class="{{$baseClass}}__top">
        @avatar([
            'name' => $author,
            'icon' => [
                'name' => $icon,
                'size' => 'lg'
            ],
            'image' => $image
        ])
        @endavatar

        @typography([
            "variant" => "title",
            "element" => "h6",
            "classList" => [$baseClass.'__author']
        ])
            {{$author}}
        @endtypography

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