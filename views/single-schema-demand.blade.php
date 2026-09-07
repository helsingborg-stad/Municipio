@extends('templates.single')

@section('article.content')
    {!!$post->getSchemaProperty('description') ?? ''!!}
@stop

@section('sidebar.right-sidebar.before')

    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']])
        {{$lang->information}}
    @endtypography

    @if(!empty($informationList))
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
    @endif

    @if($post->getSchemaProperty('url'))
        @button([
            'classList' => ['u-margin__top--4'],
            'fullWidth' => true,
            'text' => $lang->moreInfo,
            'color' => 'primary',
            'style' => 'filled',
            'href' => $post->getSchemaProperty('url')
        ])@endbutton
    @endif

@stop
