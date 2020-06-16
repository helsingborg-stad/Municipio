<article id="article" class="c-article s-article">

    <!-- Feature Image -->
    @if (isset($feature_image->src))
		@image([
			'src'=> $feature_image->src,
			'alt' => $feature_image->alt,
			'caption' => $feature_image->title,
			'classList' => ['c-article__feature-image']
		])
		@endimage
    @endif

    <!-- Title -->
    @typography(["element" => "h1"])
        {!! $postTitleFiltered !!}
    @endtypography

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

	@includeIf('comments')
	
</article>


{{--
<article id="article" class="c-article s-article u-mb-4">
	@typography([
		"variant" => "h1",
		"element" => "h1"
	])
	{{ $postTitle }}
	@endtypography
	@includeIf('partials.accessibility-menu')


	@if (get_field('post_single_show_featured_image') === true)
		@image([
			'src'=> municipio_get_thumbnail_source(null, array(700,700)),
			'alt' => the_title()
		])
		@endimage
	@endif

	@if (post_password_required($post))
		{!! get_the_password_form() !!}
	@else
		@if (isset(get_extended($post->post_content)['main']) && strlen(get_extended($post->post_content)['main']) > 0 && isset(get_extended($post->post_content)['extended']) && strlen(get_extended($post->post_content)['extended']) > 0)

			{!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
			{!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}



		@else
			@if (substr($post->post_content, -11) == '<!--more-->')
			{!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
			@else
			{!! the_content() !!}
			@endif

		@endif
	@endif

</article>
--}}