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
                'list' => [['label' => '1'], ['label' => '2'], ['label' => '3']]
            ])
            @endlisting
        @endgroup
    @endpaper
@stop

@section('below')
    @if(!empty($guides))
    <div class="o-grid">
        @group([
            'justifyContent' => 'space-between',
        ])
        @typography([
            'element' => 'h2',
        ])
            {{$labels['relatedGuides']}}
        @endtypography
        @link([
            'href' => get_post_type_archive_link('guide'),
        ])
        {{$labels['showAll']}}
        @endlink
        @endgroup
        @foreach($guides as $guide)
         <div class="o-grid-4@md u-margin__bottom--8">
            @segment([
                'layout' => 'card',
                'title' => $guide->postTitle,
                'content' => $guide->excerpt,
                'image' => $guide->thumbnail['src'],
                'buttons' => [['text' => 'Read more', 'href' => $guide->permalink]],
                'tags' => $guide->terms,
                'meta' => $guide->readingTime,
            ])
            @endsegment
         </div>
        @endforeach
    </div>
    @endif
@stop