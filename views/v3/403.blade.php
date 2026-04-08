@extends('templates.master')

@section('content')
    @scope(['name' => ['error', 'error-403']])
        @includeFirst(['partials.403.' . $postType, 'partials.403.default'])
    @endscope
@stop
