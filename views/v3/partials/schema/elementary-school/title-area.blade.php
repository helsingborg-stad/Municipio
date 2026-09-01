@typography(['element' => 'h1', 'variant' => 'h1', 'id' => 'page-title'])
    {!! $post->getTitle() !!}
@endtypography

@includeWhen(!empty($alert), 'partials.schema.elementary-school.alert', ['alert' => $alert])

@typography(['variant' => 'lead'])
    {!! $preamble !!}
@endtypography