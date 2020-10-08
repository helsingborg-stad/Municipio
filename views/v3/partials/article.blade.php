<article id="article" class="c-article s-article">
    <!-- Title -->
    @typography(["element" => "h1"])
        {!! $postTitleFiltered !!}
    @endtypography

    <!-- Blog style author signature -->
    @if(!$postTypeDetails->hierarchical && $postType == 'post')
        @signature([
            'author' => $authorName, 
            'avatar_size' => 'sm',
            'avatar' => $authorAvatar,
            'authorRole' => $authorRole,
            'link' => $signature->link
        ])
        @endsignature
    @endif

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
            'author' => $signature->name, 
            'published' => $signature->publish,
            'updated' => $signature->updated,
            'avatar_size' => 'sm',
            'avatar' => $signature->avatar,
            'authorRole' => $signature->role,
            'link' => $signature->link
        ])
        @endsignature
    @elseif(!$postTypeDetails->hierarchical && $postType == 'post')
        @signature([
            'published' => $signature->publish,
            'updated' => $signature->updated,
        ])
        @endsignature
    @endif

	@includeIf('partials.comments')
	
</article>