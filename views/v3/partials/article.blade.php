<article id="article" class="c-article c-article--readable-width s-article u-clearfix">
    
    @section('article-before-title')
    @show
    
    <!-- Title -->
    @typography(["element" => "h1"])
        {!! $postTitleFiltered !!}
    @endtypography

    @section('article-after-title')
    @show

    <!-- Blog style author signature -->
    @if(!$postTypeDetails->hierarchical && $isBlogStyle)
        @signature([
            'author'            => $signature->name,
            'avatar_size'       => 'sm',
            'avatar'            => $signature->avatar,
            'authorRole'        => $signature->role,
            'link'              => $signature->link,
            'published'         => $signature->published,
            'updated'           => $signature->updated,
            'updatedLabel'      => $publishTranslations->updated,
            'publishedLabel'    => $publishTranslations->publish,
            'classList'         => ['u-margin__y--2']
        ])
        @endsignature
    @endif

    @section('article-before-featured-image')
    @show

    @if (!empty($featuredImage->src))
        @image([
            'src'=> $featuredImage->src[0],
            'alt' => $featuredImage->alt,
            'classList' => ['c-article__feature-image', 'u-box-shadow--1']
        ])
        @endimage
    @endif

    @section('article-after-featured-image')
    @show

    @section('article-before-content')
    @show

	<!-- Content -->
	{!! $postContentFiltered !!}

    @section('article-after-content')
    @show

    @section('article-before-terms')
    @show

    <!-- Terms -->
    @if(isset($terms))
        @tags(['tags' => $terms])
        @endtags
    @endif

    @section('article-after-terms')
    @show

    @section('article-before-comments')
    @show

    <!-- Comments -->
	@includeIf('partials.comments')

    @section('article-after-comments')
    @show
	
</article>