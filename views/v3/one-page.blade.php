@extends('templates.master')
@section('layout')
    <div class="o-container">

      {!! $hook->innerLoopStart !!}

      @if($hasBlocks && $post)
          {!! $post->postContentFiltered !!}
      @endif

      {!! $hook->innerLoopEnd !!}

      @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])
      @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])
      @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])

    </div>
@stop