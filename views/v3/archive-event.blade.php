@extends('templates.archive')

@section('loop')
    @includefirst(
        ['partials.post.post-event'],
        ['posts' => $posts]
    )
@stop