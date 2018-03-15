@extends('templates.master')

@section('content')
    @includeFirst(['partials.404.' . $post_type, 'partials.404.default'])
@stop
