@extends('templates.master')

@section('hero-top-sidebar')
    @includeIf('partials.hero')
    @includeIf('partials.sidebar', ['id' => 'top-sidebar'])    
@stop

@section('layout')
    <div class="o-container">

      @includeIf('partials.navigation.helper')

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