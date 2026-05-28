@extends('templates.master')

@section('content')
    @scope(['name' => ['error', 'error-404']])
        @includeFirst(['partials.404.' . $postType, 'partials.404.default'])
    @endscope
@stop
