@extends('widget.header-links.partials.button')

@section('content')
    <span class="hamburger-box">
      <span class="hamburger-inner"></span>
    </span>
    @if (!isset($link['hide_text']) || !$link['hide_text'])
        <span class="hamburger-label">{{$link['text']}}</span>
    @endif
@endsection

