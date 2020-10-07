<article id="article" class="c-article s-article">
    <!-- Title -->
    @typography(["element" => "h1"])
        {!! $postTitleFiltered !!}
    @endtypography

    <!-- Feature Image -->
    @if (isset($feature_image->src))
		@image([
			'src'=> $feature_image->src[0],
			'alt' => $feature_image->alt,
			'caption' => $feature_image->title,
			'classList' => ['c-article__feature-image']
		])
		@endimage
    @endif

	<!-- Content -->
	{!! $postContentFiltered !!}

    <!-- Signature -->
    @if($postTypeDetails->hierarchical)
        @signature([
            'author' => $authorName, 
            'published' => $publishedDate,
            'updated' => $updatedDate,
            'avatar_size' => 'sm',
            'avatar' => $authorAvatar
        ])
        @endsignature
    @endif

	@includeIf('partials.comments')
	
</article>