{!! $hook->innerLoopStart !!}

@if (!empty($post))
    @include('partials.article', array_merge((array) $post, ['featuredImage' => $featuredImage]))
@endif

{!! $hook->innerLoopEnd !!}
