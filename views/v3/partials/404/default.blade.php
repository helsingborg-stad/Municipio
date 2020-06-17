@typography(["element" => "h1"])
    {{ $heading }}
@endtypography

@typography(["element" => "span"])
    {{ $subheading }}
@endtypography

@foreach($actionButtons as $button) 
    @button([
        'text' => $button->label,
        'color' => 'primary',
        'style' => 'basic',
        'href' => $button->href
    ])@endbutton
@endforeach