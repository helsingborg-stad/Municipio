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

            @typography([ 'element' => 'b', ])
                {{$progressLabel}}
            @endtypography
            @progressBar([ 'value' => $progressPercentage ]) @endprogressBar

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
                    @if(is_array($item['value']))
                        @foreach ($item['value'] as $value)
                            @if(!empty($value))
                                @typography(){!!$value!!}@endtypography
                            @endif
                        @endforeach
                    @else
                        @typography(){{$item['value']}}@endtypography
                    @endif
                @endcollection__item
            @endforeach
        @endcollection
    @endpaper

@stop