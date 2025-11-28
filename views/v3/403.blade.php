@extends('templates.master')

@section('content')
    @includeFirst(['partials.403.' . $postType, 'partials.403.default'])
@stop
