@grid([
    "classList" => [
        $baseClass.'__expanded_menu',
    ],
    "container" => true,
    "max_width" => "max-content",
    "col_gap" => "4"
])

    @foreach ($expanded_menu as $key => $item)
        @grid([])
            @link([
                'href' => $item['href']
            ])
                {{$key}}
            @endbutton
        @endgrid
    @endforeach
@endgrid