@extends('templates.master')

@section('content')
    @includeFirst(['partials.401.' . $postType, 'partials.401.default'])
@stop