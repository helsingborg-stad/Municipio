@extends('templates.single')

@section('content')
<div class="content u-margin__left--auto@md u-margin__right--auto@md">
    {!! $hook->loopStart !!}

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    @section('loop')
    @includeIf('partials.loop')
    @show

    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

    @includeWhen($quicklinksPlacement === 'below_content', 'partials.navigation.fixed')

    {!! $hook->loopEnd !!}
</div>
@stop
@section('sidebar-right')
@stop

@section('below')
<div class="content u-margin__left--auto u-margin__right--auto">
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])
    @includeWhen(empty($isBlogStyle), 'partials.signature', [
    'classList' => ['u-margin__y--2'],
    ])
</div>
<div class="content u-margin__left--auto u-margin__right--auto">
    <!-- Comments -->
    @section('article.comments.before')@show
    @includeIf('partials.comments')
    @section('article.comments.after')@show
</div>
@stop
