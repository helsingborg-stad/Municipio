@extends('templates.single')


@section('hero-top-sidebar')
    @if (!empty($featuredImage->src[0]))
        @hero([
            'image' => $featuredImage->src[0]
        ])
        @endhero
    @endif
    @if (!$placeQuicklinksAfterContent)
        @include('partials.navigation.fixed')
    @endif
    <div class="o-container">
        @paper([
            'attributeList' => [
                'style' => !empty($featuredImage->src[0]) ? 'transform:translateY(calc(max(-50%, -50px)))' : 'margin-top: 32px'
            ],
            'classList' => ['o-grid', 'u-padding--6']

        ])
            @group([
                'direction' => 'vertical',
                'classList' => ['o-grid-12@sm', 'o-grid-9@md', 'o-grid-9@lg']
            ])
                @typography([
                    'element' => 'h1',
                    'variant' => 'h1'
                ])
                    {{ $post->postTitle }}
                @endtypography
                @typography([
                    'classList' => ['u-padding__top--4']
                ])
                    {!! $post->postContentFiltered !!}
                @endtypography
            @endgroup
            <div class="o-grid-12@sm o-grid-3@md o-grid-3@lg u-display--flex u-width--100 u-justify-content--end@md u-justify-content--end@lg">
                @listing([
                    'list' => $list,
                    'icon' => false,
                    'classList' => ['unlist', 'u-padding__top--4@xs', 'u-padding__top--4@sm'],
                    'padding' => 4,
                    ])
                @endlisting
            </div>
        @endpaper
    </div>

    @if ($placeQuicklinksAfterContent)
        @include('partials.navigation.fixed')
    @endif

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
                    {{ $labels['related'] }} {{ $postType }}
                @endtypography
                @if (!empty(get_post_type_archive_link($postType)))
                    @link([
                        'href' => get_post_type_archive_link($postType)
                    ])
                        {{ $labels['showAll'] }}
                    @endlink
                @endif
            @endgroup
            @foreach ($posts as $post)
                <div class="o-grid-4@md u-margin__bottom--8">
                    @segment([
                        'layout' => 'card',
                        'title' => $post->postTitle,
                        'content' => $post->excerptShort,
                        'image' => $post->thumbnail['src'],
                        'buttons' => [['text' => 'Read more', 'href' => $post->permalink]],
                        'tags' => $post->terms,
                        'meta' => $post->readingTime
                    ])
                    @endsegment
                </div>
            @endforeach
        </div>
    @endforeach
@stop
