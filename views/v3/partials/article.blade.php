<article id="article" class="c-article s-article u-mb-4">
   <!-- Title -->
	@typography(["element" => "h1"])
		{!! $postTitleFiltered !!}
	@endtypography
   <!-- Excerpt ? Byline ...-->
	@typography([
		"element" => "p",
		"classList" => ["byline"]
	])
	   <!-- Post Excerpt ?????-->
	@endtypography
	<!-- Content -->
	@paper(['padding' => 3])
		{!! $postContentFiltered !!}


	{{-- Author --}}
	@grid([
		"container" => true,
		"row_gap" => 3
	])
		@grid([
			"col_gap" => 8,
			"col" => [
				"xs" => [1,3],
				"sm" => [1,3],
				"md" => [1,3],
				"lg" => [1,2],
				"xl" => [1,2],
				"lg" => [1,2],
				"xl" => [1,2]
			],
			"row" => [
				"xs" => [1,2],
				"sm" => [1,2],
				"md" => [1,2]
			]
		])

			@avatar(['size' => 'sm', 'image' => $authorAvatar, 'classList' => ['author']])
			@endavatar

		@endgrid


		@grid([
			"row_gap" => 0,
			"col" => [
				"xs" => [3,12],
				"sm" => [2,7],
				"md" => [2,7],
				"lg" => [2,5],
				"xl" => [2,4]
			],
			"row" => [
				"xs" => [1,2],
				"sm" => [1,2],
				"md" => [1,2],
				"lg" => [1,2],
				"xl" => [1,1]
			]
		])

		@typography(['variant' => 'h4', 'element' => 'meta'])
			{{$publishTranslations['by']}} {{$authorName}}
		@endtypography

		@typography(['variant' => 'meta'])
			{{$publishTranslations['published']}} {{$publishedDate}}
		@endtypography

		@typography(['variant' => 'meta'])
			{{$publishTranslations['updated']}} {{$updatedDate}}
		@endtypography

		@endgrid
	@endgrid

	@endpaper

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