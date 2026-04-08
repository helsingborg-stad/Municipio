@extends('templates.grid', [
    'layoutData' => [
        'addToDefaultClassList' => ['u-margin__bottom--4']
    ],
    'belowSidebarClasses' => ['o-container'],
])

@section('hero-top-sidebar')
    @includeWhen($post->getImage(), 'partials.schema.place.hero')
    @parent
@stop

@section('layout')
    @scope(['name' => ['single-schema-place', $postType . '-single-schema-place']])
        @section('before-content')
            @includeWhen($post->getTitle(), 'partials.schema.place.title-area')
        @stop

        @section('content')
            @includeWhen($post->getContent(), 'partials.schema.place.description')
        @stop

        @section('sidebar-right-content')
            @includeWhen(!empty($placeInfoList), 'partials.schema.place.place-info')
            @includeWhen(!empty($placeActions), 'partials.schema.place.actions')
        @stop

        {{-- Wrapping the main section in a paper --}}
        @paper([
            'attributeList' => [
                'style' => !empty($featuredImage['src']) ? 'transform:translateY(calc(max(-50%, -50px)))' : 'margin-top: 32px'
            ],
            'classList' => ['u-padding--6']
        ])
            @include('templates.sections.grid.content', [
                'addToArticleClassList' => ['c-article'],
                'addToRightSidebarClassList' => ['u-justify-content--end@md', 'u-justify-content--end@lg', 'u-justify-content--end@xl']
            ])
        @endpaper
    @endscope
@stop

@section('below')
    @include('partials.navigation.fixed')
    @parent
    @includeWhen(!empty($relatedPosts), 'partials.schema.place.related-posts')
@stop