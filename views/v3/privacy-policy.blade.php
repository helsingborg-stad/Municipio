@extends('templates.single')

@section('content')
    @scope(['name' => ['template', 'privacy-policy']])
        @element([
            'componentElement' => 'article',
            'id' => 'article',
            'classList' => array_merge(
                (isset($centerContent) && $centerContent) ? ['u-margin__x--auto'] : [],
                ['c-article', 'c-article--readable-width', 's-article', 'u-clearfix', 't-privacy-policy-page']
            ),
        ])
            @typography([
                'element' => 'h1',
                'variant' => 'h1',
                'id' => 'page-title',
                'useHeadingsContext' => false,
            ])
                {!! $heading !!}
            @endtypography

            {!! $content !!}

            @if (!empty($lastUpdated))
                @typography(['element' => 'p', 'variant' => 'meta', 'classList' => ['u-margin__top--4']])
                    {{ $lang->lastUpdated }}: {{ $lastUpdated }}
                @endtypography
            @endif
        @endelement
    @endscope
@stop
