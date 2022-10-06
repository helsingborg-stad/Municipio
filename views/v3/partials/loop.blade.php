{!! $hook->innerLoopStart !!}

@if (!empty($post))
    @include('partials.article', (array) $post)
@endif

{!! $hook->innerLoopEnd !!}
