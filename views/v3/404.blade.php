@extends('templates.master')

@section('content')
    @includeFirst(['partials.404.' . $postType, 'partials.404.default'])
@stop
