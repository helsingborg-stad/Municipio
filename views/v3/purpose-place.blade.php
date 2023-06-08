@extends('templates.single')

@section('hero-top-sidebar')

    @if (!empty($featuredImage['src']))
        @hero([
            'image' => $featuredImage['src'],
        ])
        @endhero
    @endif
    @includeWhen(!$placeQuicklinksAfterContent, 'partials.navigation.fixed')

    @includeWhen($displayMap, 'partials.openstreetmap.singular')

    @includeWhen(!$displayMap, 'partials.purpose.article.paper')

    @includeWhen($placeQuicklinksAfterContent, 'partials.navigation.fixed')

@stop

@section('helper-navigation')
@stop

@section('content')
@stop

@section('below')
    @foreach ($relatedPosts as $postType => $posts)
        <div class="o-grid">
            @group([
                'justifyContent' => 'space-between'
            ])
                @typography([
                    'element' => 'h2'
                ])
                    {{ $lang->related }} {{ $postType }}
                @endtypography
                @if (!empty(get_post_type_archive_link($postType)))
                    @link([
                        'href' => get_post_type_archive_link($postType)
                    ])
                        {{ $lang->showAll }}
                    @endlink
                @endif
            @endgroup
            @foreach ($posts as $post)
                <div class="o-grid-4@md u-margin__bottom--8">
                    @segment([
                        'layout' => 'card',
                        'title' => $post->postTitleFiltered,
                        'content' => $post->excerptShort,
                        'image' => $post->thumbnail['src'],
                        'buttons' => [['text' => $labels['readMore'], 'href' => $post->permalink]],
                        'tags' => $post->termsUnlinked,
                        'meta' => $post->readingTime,
                        'icon' => $post->termIcon['icon'] ? $post->termIcon : false
                    ])
                    @if ($post->callToActionItems['floating'])
                        @slot('floating')
                            @icon($post->callToActionItems['floating'])
                            @endicon
                        @endslot
                    @endif
                    @endsegment
                </div>
            @endforeach
        </div>
    @endforeach
@stop
