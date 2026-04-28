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
    @yield('article.title.before')
    @hasSection('article.title')
        @yield('article.title')
    @else
        @if ((is_object($post) && method_exists($post, 'getTitle') ? $post->getTitle() : $post->post_title) || isset($callToActionItems['floating']))
            @group([
                'justifyContent' => 'space-between'
            ])
                @if ((method_exists($post, 'getTitle') ? $post->getTitle() : $post->post_title))
                    @typography([
                        'element' => 'h1', 
                        'variant' => 'h1', 
                        'id' => 'page-title',
                    ])
                        {!! (method_exists($post, 'getTitle') ? $post->getTitle() : $post->post_title) !!}
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
    @endif
    @yield('article.title.after')

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
    @if ($displayFeaturedImage && method_exists($post, 'getImage') && !empty($post->getImage()))
        @yield('article.featuredimage.before')
        @image([
            'src' => $post->getImage(),
            'caption' => $featuredImage['caption'],
            'removeCaption' => !$displayFeaturedImageCaption,
            'classList' => ['c-article__feature-image', 'u-box-shadow--1', 'u-margin__top--2'],
        ])
        @endimage
        @yield('article.featuredimage.after')
    @endif

    <!-- Content -->
    @yield('article.content.before')
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
    @hasSection('article.content')
        @yield('article.content')
    @else
        {!! is_object($post) && method_exists($post, 'getContent') ? $post->getContent() : $post->post_content !!}
    @endif
    @yield('article.content.after')

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
