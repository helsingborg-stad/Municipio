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
	@typography([])
		{!! $postContentFiltered !!}
	@endtypography	
    

    <!-- Signature -->
    @if($postTypeDetails->hierarchical)
        @typography([
            "variant" => "meta",
            "classList" => [
                "u-color__text--darker"
            ]
        ])
            <b><?php _e("Published", 'municipio')?>: </b> {{$publishedDate}}
        @endtypography

        @typography([
            "variant" => "meta",
            "classList" => [
                "u-color__text--darker"
            ]
        ])
            <b><?php _e("Last updated", 'municipio')?>: </b> {{$updatedDate}}
        @endtypography
    @endif
	
	@if(isset($permalink))
		@typography(['variant' => 'meta'])
			@link(['href' => $permalink])
				{{$permalink}}
			@endlink
		@endtypography
	@endif

	@includeIf('partials.comments')
	
</article>