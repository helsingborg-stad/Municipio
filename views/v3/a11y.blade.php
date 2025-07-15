@extends('templates.single')

@section('content')

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
        @typography([
            'element' => 'h1', 
            'variant' => 'h1', 
            'id' => 'page-title',
        ])
            {!! $heading !!}
        @endtypography

        {!! $content !!}
    @endelement
@stop