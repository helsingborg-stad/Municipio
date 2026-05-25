@extends('templates.master')

@section('content')
    @scope(['name' => ['error', 'error-401']])
        @includeFirst(['partials.401.' . $postType, 'partials.401.default'])
    @endscope
@stop