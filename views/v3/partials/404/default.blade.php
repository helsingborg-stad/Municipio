@typography([
    "element" => "h1",
    "slot" => $heading
])
@endtypography

@typography([
    "element" => "span",
    "slot" => $subheading
])
@endtypography

@foreach($actionButtons as $button) 
    @button([
        'text' => $button->label,
        'color' => 'primary',
        'style' => 'basic',
        'href' => $button->href

    ])
    @endbutton
@endforeach