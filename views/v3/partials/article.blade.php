<article class="c-article c-article--readable-width s-article u-clearfix" id="article" {!! !empty($postLanguage) ? 'lang="' . $postLanguage . '"' : '' !!}>

    <!-- Title -->
    @section('article.title.before')@show
    @section('article.title')
        @if ($post->postTitleFiltered || isset($callToActionItems['floating']))
            @group([
                'justifyContent' => 'space-between'
            ])
                @if ($post->postTitleFiltered)
                    @typography([
                        'element' => 'h1', 
                        'variant' => 'h1', 
                        'id' => 'page-title',
                    ])
                        {!! $post->postTitleFiltered !!}
                    @endtypography
                @endif
                @if (!empty($callToActionItems['floating']))
                    @icon($callToActionItems['floating'])
                    @endicon
                @endif
            @endgroup
        @endif
    @show
    @section('article.title.after')@show

    <!-- Blog style author signature -->
    @includeWhen(
        (!$postTypeDetails->hierarchical || $isBlogStyle), 
        'partials.signature',
        array_merge(
            (array) $signature, 
            (array) ['classList' => []]
        )
    )

    <!-- Featured image -->
    @if ($displayFeaturedImage && $featuredImage['src'])
        @section('article.featuredimage.before')@show
        @if (!empty($featuredImage['src']))
            @image([
                'src' => $featuredImage['src'],
                'alt' => $featuredImage['alt'] ?? '',
                'classList' => ['c-article__feature-image', 'u-box-shadow--1']
            ])
            @endimage
        @endif
        @section('article.featuredimage.after')@show
    @endif

    <!-- Content -->
    @section('article.content.before')@show
    {!! $hook->articleContentBefore !!}
    @if ($postAgeNotice)
        @notice([
            'message' => [
                'text' => $postAgeNotice
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
    @section('article.content')
        {!! $post->postContentFiltered !!}
    @show
    @section('article.content.after')@show

    <!-- Terms -->
    @section('article.terms.before')@show
    @if (isset($terms))
        @tags(['tags' => $terms])
        @endtags
    @endif
    @section('article.terms.after')@show

    <!-- Blog style author signature -->
    @section('content.below')
        @includeWhen(
            ($postTypeDetails->hierarchical && !$isBlogStyle), 
            'partials.signature',
            array_merge(
                (array) $signature, 
                (array) ['classList' => []]
            )
        )
    @endsection

</article>
