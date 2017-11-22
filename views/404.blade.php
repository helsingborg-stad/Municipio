@extends('templates.master')

@section('content')



@if ($is_event)
  
  @include('event404')
   

@else

 @include('default404')

@endif
@stop
