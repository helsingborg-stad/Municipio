@grid([
    "classList" => [
        $baseClass.'__expanded_grid'
    ],
    "columns" => "auto-fill",
    "max_width" => "340px",
    "container" => true,
    "col_gap" => "5",
    "row_gap" => "9"
])

    @foreach ($expanded_menu as $key => $item)
        @grid([
            'element' => 'a',
            'classList' => [
                'u-margin__y--4'
            ],
            'attributeList' => [
                'href' => $item['href']
            ]
        ])
            @typography([
                "element" => "h2",
                "variant" => "h2",
                "classList" => [
                    $baseClass.'__title'
                ],
            ])
                {{$key}}
            @endtypography

            @typography([
                "element" => "p",
                "variant" => "body"
            ])
                {{isset($item['preview']) ? $item['preview'] : "No Preview Available"}}
            @endtypography
        @endgrid
    @endforeach
    
@endgrid