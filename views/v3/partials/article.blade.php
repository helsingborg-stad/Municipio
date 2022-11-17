<article id="article" class="c-article c-article--readable-width s-article u-clearfix" 
@if($postLanguage)
	lang="{{ $postLanguage }}"
@endif
>
    
    <!-- Title -->
    @section('article.title.before')@show
    @if($postTitleFiltered)
        @typography(["element" => "h1", "variant" => "h1"])
            {!! $postTitleFiltered !!}
        @endtypography
    @endif
    @section('article.title.after')@show

    <!-- Blog style author signature -->
    @if(!$postTypeDetails->hierarchical && $isBlogStyle)
        @section('article.signature.after')@show
        @signature([
            'author'            => $signature->name,
            'avatar_size'       => 'sm',
            'avatar'            => $signature->avatar,
            'authorRole'        => $signature->role,
            'link'              => $signature->link,
            'published'         => $signature->published,
            'updated'           => $signature->updated,
            'updatedLabel'      => $publishTranslations->updated,
            'publishedLabel'    => $publishTranslations->publish
        ])
        @endsignature
        @section('article.signature.after')@show
    @endif

    <!-- Featured image -->
    @section('article.featuredimage.before')@show
    @if (!empty($featuredImage->src))
        @image([
            'src'=> $featuredImage->src[0],
            'alt' => $featuredImage->alt,
            'classList' => ['c-article__feature-image', 'u-box-shadow--1']
        ])
        @endimage
    @endif
    @section('article.featuredimage.after')@show

	<!-- Content -->
    @section('article.content.before')@show
	@if($postAgeNotice)
		@notice([
			'message' => [
				'text' => $postAgeNotice,
			],
			'type' => 'info',
			'icon' => [
				'name' => 'lock_clock',
				'size' => 'md',
				'color' => 'white'
			]
		])
		@endnotice
	@endif
	{!! $postContentFiltered !!}
    @section('article.content.after')@show

    <!-- Terms -->
    @section('article.terms.before')@show
    @if(isset($terms))
        @tags(['tags' => $terms])
        @endtags
    @endif
    @section('article.terms.after')@show

</article>
