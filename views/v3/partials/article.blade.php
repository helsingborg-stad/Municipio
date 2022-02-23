<article id="article" class="c-article c-article--readable-width s-article u-clearfix">
    
    <!-- Title -->
    @section('article.title.before')@show
    @typography(["element" => "h1", "variant" => "h1"])
        {!! $postTitleFiltered !!}
    @endtypography
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

<!-- Comments -->
@section('article.comments.before')@show
@includeIf('partials.comments')
@section('article.comments.after')@show