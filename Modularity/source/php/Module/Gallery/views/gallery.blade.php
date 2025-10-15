@if (!$hideTitle && !empty($postTitle))
    @typography([
        'element' => 'h2', 
        'variant' => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif

@if ($images)
    @gallery([
        'list' => $images,
        'classList' => ['image-gallery'],
        'ariaLabels' => $ariaLabels,
    ])
    @endgallery
@endif
