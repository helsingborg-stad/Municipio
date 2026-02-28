@typography(['element' => 'h1', 'variant' => 'h1', 'id' => 'page-title'])
    {!! $post->getTitle() !!}
@endtypography

@element(['classList' => ['lead']])
    {!! $preamble !!}
@endelement