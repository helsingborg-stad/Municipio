@card([
    'heading' => apply_filters('the_title', $post_title),
    'context' => 'module.iframe'
])
    @if (!$hideTitle && !empty($postTitle))
        <div class="c-card__header">
            @typography([
                'element' => 'h2',
                'variant' => 'h4',
                'classList' => ['card-title']
            ])
                {!! $postTitle !!}
            @endtypography
        </div>
    @endif
    @iframe([
        'src' => $url,
        'height' => $height,
        'title' => $description ?? $post_title,
        'labels' => $lang,
    ])
    @endiframe
@endcard
