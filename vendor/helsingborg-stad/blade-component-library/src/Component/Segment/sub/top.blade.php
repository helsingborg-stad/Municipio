<div class="{{ $baseClass }}__top">

    @if (!empty($top))
        {{ $top }}
    @endif

    @if($title)
        @typography(["variant" => "h1",
            "element" => "h2",
            'classList' => [$baseClass.'__heading']
        ])
            {{ $title }}
        @endtypography
    @endif

    @if($sub_title)
        @typography([
            'variant' => 'body',
            'element' => 'p',
            'classList' =>  [$baseClass.'__body']
        ])
            {{ $sub_title }}
        @endtypography
    @endif
</div>