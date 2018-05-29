@extends('templates.single')

@section('sidebar-left')
@stop

@section('content')
@include('components.dynamic-sidebar', ['id' => 'content-area'])
@stop

@section('sidebar-right')
@stop
