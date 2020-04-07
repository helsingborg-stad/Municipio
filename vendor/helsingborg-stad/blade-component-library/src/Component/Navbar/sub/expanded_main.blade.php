<div class="{{$baseClass}}__expanded_main">
    @if ($expanded_prev)
        @button([
            'icon' => 'arrow_back',
            'reversePositions' => true,
            'text' => $expanded_prev,
            'color' => 'default',
            'size' => 'lg',
            'style' => 'basic',
            'classList' => [
                $baseClass.'__prev'
            ]
        ])
        @endbutton
    @endif

    @typography([
        "element" => "h2",
        "variant" => "marketing",
        "classList" => [
            "u-margin__top--8"
        ]
    ])
        {{$expanded_current}}
    @endtypography

    @include('Navbar.sub.expanded_grid')
</div>