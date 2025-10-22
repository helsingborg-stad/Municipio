@if (!$hideTitle && !empty($postTitle))
        @typography([
            'element' => 'h2', 
            'variant' => 'h2', 
            'classList' => ['module-title']
        ])
            {!! $postTitle !!}
        @endtypography
@endif

@if (!empty($imageLink))
    @link([
        'href' => $imageLink,
        'classList' => ['u-no-decoration'],
    ])
        @include('partials.image')
    @endlink
@else
    @include('partials.image')
@endif
