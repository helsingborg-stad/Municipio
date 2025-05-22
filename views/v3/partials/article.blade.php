@element([
    'componentElement' => 'article',
    'id' => 'article',
    'attributeList' => array_merge(
        !empty($postLanguage) ? ['lang' => $postLanguage] : [],
    ),
    'classList' => array_merge(
        (isset($centerContent) && $centerContent) ? ['u-margin__x--auto'] : [],
        [ 'c-article', 'c-article--readable-width', 's-article', 'u-clearfix' ]
    ),
])

    <!-- Title -->
    @section('article.title.before')@show
    @section('article.title')
        @if ($postTitleFiltered || isset($callToActionItems['floating']))
            @group([
                'justifyContent' => 'space-between'
            ])
                @if ($postTitleFiltered)
                    @typography([
                        'element' => 'h1', 
                        'variant' => 'h1', 
                        'id' => 'page-title',
                    ])
                        {!! $postTitleFiltered !!}
                    @endtypography
                @endif
                @if (!empty($callToActionItems['floating']['icon']) && !empty($callToActionItems['floating']['wrapper']))
                    @element($callToActionItems['floating']['wrapper'] ?? [])
                        @icon($callToActionItems['floating']['icon'])
                        @endicon
                    @endelement
                @endif
            @endgroup
        @endif
    @show
    @section('article.title.after')@show

    <!-- Blog style author signature -->
    @includeWhen(
        (($postTypeDetails && !$postTypeDetails->hierarchical) || $isBlogStyle),
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
                'caption' => $featuredImage['caption'],
                'removeCaption' => !$displayFeaturedImageCaption,
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
        {!! $postContentFiltered !!}
    @show
    @section('article.content.after')@show

    <!-- Terms -->
    @section('article.terms.before')@show
    @if (isset($terms))
        @tags(['tags' => $terms])
        @endtags
    @endif
    @section('article.terms.after')@show

    {!! $hook->articleContentAfter !!}

    <!-- Blog style author signature -->
    @section('content.below')
        @includeWhen(
            (($postTypeDetails && $postTypeDetails->hierarchical) && !$isBlogStyle),
            'partials.signature',
            array_merge(
                (array) $signature, 
                (array) ['classList' => []]
            )
        )
    @endsection

@endelement
