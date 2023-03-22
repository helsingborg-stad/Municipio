@extends('templates.single')


@section('hero-top-sidebar')
    @hero([
        'image' => $featuredImage->src[0],
    ])
    @endhero
    @paper([
        'classList' => ['u-width--75@md', 'u-width--75@lg', 'u-margin__x--auto@md', 'u-margin__x--auto@lg'],
        'attributeList' => ['style' => 'transform:translateY(calc(max(-50%, -50px)))'],
    ])
        @group([
            'justifyContent' => 'space-between',
            'classList' => ['u-padding--6']
        ])
            @group([
                'direction' => 'vertical'
            ])
                @typography([
                    'element' => 'h1',
                    'variant' => 'h1',
                ])
                {{$post->postTitle}}
                @endtypography
                @typography([

                ])
                {!! $post->postContent !!}
                @endtypography
            @endgroup
            @listing([
                'list' => $list,
                'icon' => false
            ])
            @endlisting
        @endgroup
    @endpaper
@stop

@section('helper-navigation')
@stop

@section('content')
@stop

@section('below')
    @foreach($relatedPosts as $postType => $posts) 
        <div class="o-grid">
            @group([
                    'justifyContent' => 'space-between',
                ])
                @typography([
                    'element' => 'h2',
                ])
                    {{$labels['related']}} {{$postType}}
                @endtypography
                @link([
                    'href' => get_post_type_archive_link($postType),
                ])
                {{$labels['showAll']}}
                @endlink
            @endgroup
            @foreach($posts as $post) 
                <div class="o-grid-4@md u-margin__bottom--8">
                    @segment([
                        'layout' => 'card',
                        'title' => $post->postTitle,
                        'content' => $post->excerpt,
                        'image' => $post->thumbnail['src'],
                        'buttons' => [['text' => 'Read more', 'href' => $post->permalink]],
                        'tags' => $post->terms,
                        'meta' => $post->readingTime,
                    ])
                    @endsegment
                </div>
            @endforeach
        </div>
    @endforeach
@stop