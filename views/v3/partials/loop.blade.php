
{!! $hook->innerLoopStart !!}

@if($post)
    @include('partials.article', (array) $post)
@endif

{!! $hook->innerLoopEnd !!}
