
    @typography(["element" => "h1", "id" => "header404", "classList" => ["c-typhography--404-heading"]])
        {{ $heading }}
    @endtypography

    @typography(["element" => "span", "id" => "content404", "classList" => ["c-typhography--404-subheading"]])
        {{ $subheading }}
    @endtypography

    @grid(["col_gap" => 2,"row_gap" => 8, 'container' => true])
        @foreach($actionButtons as $button) 
            @button([
                'text' => $button->label,
                'href' => $button->href,
                'color' => $loop->first ? 'primary' : 'secondary',
                'style' => 'filled'
            ])
            @endbutton
        @endforeach
    @endgrid
