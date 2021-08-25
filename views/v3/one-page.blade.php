@extends('templates.master')

@section('layout')

  {!! $hook->innerLoopStart !!}

  @if($hasBlocks && $post)
      {!! $post->postContentFiltered !!}
  @endif

  {!! $hook->innerLoopEnd !!}

@stop