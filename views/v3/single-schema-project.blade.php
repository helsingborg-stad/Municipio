@extends('templates.single')

@section('hero-top-sidebar')
    @hero([ 'image' => $image, 'heroView' => 'twoColumn', 'classList' => ['u-color__bg--lightest'] ])
        @slot('content')
            @if (!empty($technology))
                @typography([ 'element' => 'span', 'classList' => ['page-header_meta'] ]) {!! $technology !!} @endtypography
            @endif
            @typography([
                'element' => 'h1',
                'variant' => 'h1',
                'classList' => ['page-header__title', 'u-margin__top--0', 'u-margin__bottom--3']
            ])
                {{ $post->postTitle }}
            @endtypography

            @if (!empty($status))
                @typography([
                    'element' => 'b',
                ])
                    {{$status}}
                @endtypography
            @endif
            @progressBar([ 'value' => $progress ]) @endprogressBar

        @endslot
    @endhero
@stop

@section('article.title')@stop
@section('article.content')
    {!!$post->postContent!!}
@stop

@section('sidebar.right-sidebar.before')
    @paper(['padding' => 2])
        @collection()
            @foreach ($informationList as $item)
                @collection__item([])
                    @typography(['element' => 'h2', 'variant' => 'h3'])
                        {{$item['label']}}
                    @endtypography
                    @typography([])
                        {{$item['value']}}
                    @endtypography
                @endcollection__item
            @endforeach
        @endcollection
    @endpaper

@stop