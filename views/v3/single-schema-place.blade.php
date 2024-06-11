@extends('templates.single')
@section('hero-top-sidebar')
    @if (!empty($featuredImage['src']))
        @hero([
            'image' => $featuredImage['src'],
        ])
        @endhero
    @endif
    @includeWhen(!$placeQuicklinksAfterContent, 'partials.navigation.fixed')
    <div class="o-container">
        @paper([
            'attributeList' => [
                'style' => !empty($featuredImage['src']) ? 'transform:translateY(calc(max(-50%, -50px)))' : 'margin-top: 32px'
            ],
            'classList' => ['u-padding--6']
        ])
            @group([
                'justifyContent'=> 'space-between',
            ])
                @typography([
                    'element' => 'h1',
                    'variant' => 'h1'
                ])
                    {!! $post->postTitleFiltered !!}
                @endtypography
                @if (!empty($post->callToActionItems['floating']))
                    @icon($post->callToActionItems['floating'])
                    @endicon
                @endif
            @endgroup
            <div class="o-grid">
                <div class="o-grid-12@sm o-grid-9@md o-grid-9@lg o-grid-9@xl">
                    @typography([])
                        {!! $post->postContentFiltered !!}
                    @endtypography
                </div>
                <div class="o-grid-12@sm o-grid-3@md o-grid-3@lg o-grid-3@xl">

                    @if (!empty($post->placeInfo))
                        @listing([
                            'list' => $post->placeInfo,
                            'icon' => false,
                            'classList' => [
                                'unlist',
                                'u-padding__top--2@xs',
                                'u-padding__top--2@sm',
                                'u-padding__bottom--3',
                                'u-margin__top--2'
                            ],
                            'padding' => 4
                        ])
                        @endlisting
                    @endif

                    @if (!empty($post->bookingLink))
                        @button([
                            'text' => $lang->bookHere,
                            'color' => 'primary',
                            'style' => 'filled',
                            'href' => $post->bookingLink,
                            'classList' => ['u-width--100'],
                        ])
                        @endbutton
                    @endif

                </div>
            </div>
        @endpaper
    </div>

    @includeWhen($placeQuicklinksAfterContent, 'partials.navigation.fixed')

@stop

@section('helper-navigation')
@stop

@section('content')
@stop

@section('below')
    @if(!empty($relatedPosts))
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
                            'content' => !empty($post->excerptShorter) ? $post->excerptShorter : false,
                            'image' => !empty($post->thumbnail['src']) ? $post->thumbnail['src'] : false,
                            'buttons' => [['text' => $labels['readMore'], 'href' => $post->permalink]],
                            'tags' => !empty($post->termsUnlinked) ? $post->termsUnlinked : false,
                            'meta' => !empty($post->readingTime) ? $post->readingTime : false,
                            'icon' => !empty($post->termIcon['icon']) ? $post->termIcon : false
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
    @endif
@stop
