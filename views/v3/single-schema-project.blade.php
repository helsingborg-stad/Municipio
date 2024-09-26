@extends('templates.single')

@section('hero-top-sidebar')
    @hero([ 'image' => $imageUrl, 'heroView' => 'twoColumn', 'classList' => ['u-color__bg--lightest'] ])
        @slot('content')
            @typography([ 'element' => 'span', 'classList' => ['page-header_meta'] ]) {!! $category !!} @endtypography
            @typography([
                'element' => 'h1',
                'variant' => 'h1',
                'classList' => ['page-header__title', 'u-margin__top--0', 'u-margin__bottom--3']
            ])
                {{ $post->postTitle }}
            @endtypography

            @tooltip([ 'label' => $status, 'icon' => 'info' ]) @endtooltip
            @progressBar([ 'value' => $progress ]) @endprogressBar

        @endslot
    @endhero
@stop

@section('article.title')@stop
@section('article.content')
    {!!$post->postContent!!}
@stop

@section('sidebar.right-sidebar.before')

    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']])
        {{$lang->information}}
    @endtypography

    @paper(['padding' => 2])
        @collection()
            @foreach ($informationList as $item)
                @collection__item([])
                    @typography(['element' => 'h4'])
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