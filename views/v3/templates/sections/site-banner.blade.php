@section('site-banner')
    @includeIf('partials.sidebar', ['id' => 'header-area-site-banner', 'classes' => $classes ?? []])
@show