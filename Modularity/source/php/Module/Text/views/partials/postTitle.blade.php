@typography([
        "element" => "h2",
        "variant" => $variant ?? 'h4',
        "id" => 'mod-text-' . $ID .'-label'
])
        {!! $postTitle !!}
@endtypography