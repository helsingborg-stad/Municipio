@extends('templates.single')

@section('content')
@php
    echo '<pre>' . print_r( $purpose, true ) . '</pre>';
@endphp
@stop
