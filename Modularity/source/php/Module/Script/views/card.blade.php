@card([
    'heading' => !$hideTitle ? apply_filters('the_title', $post_title ?? false) : null,
    'context' => 'module.script'
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
	
	@include('partials.content')
	@if(!empty($placeholder['url']))
        @image([
            'src' => $placeholder['url'],
            'alt' => $placeholder['alt'],
            'classList' => ['box-image', 'u-print-display--inline-block', 'u-display--none']
        ])
        @endimage
    @endif
@endcard
