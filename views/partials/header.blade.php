<header id="site-header" class="site-header {{ $headerLayout }}">
    @if ($headerLayout == 'header-jumbo')
        @include('partials.header.jumbo')
    @else
        @include('partials.header.default')
    @endif
</header>

@include('partials.hero')
