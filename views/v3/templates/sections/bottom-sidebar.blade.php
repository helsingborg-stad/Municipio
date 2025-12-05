@section('bottom-sidebar')
    @includeIf('partials.sidebar', ['id' => 'bottom-sidebar', 'classes' => $classes ?? []])
@show