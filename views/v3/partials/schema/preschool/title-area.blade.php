@typography(['element' => 'h1', 'variant' => 'h1', 'id' => 'page-title', 'classList' => ['u-margin__top--0']])
    {!! $post->getTitle() !!}
@endtypography

@typography(['variant' => 'lead'])
    {!! $preamble !!}
@endtypography
