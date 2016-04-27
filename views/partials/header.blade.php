<header id="site-header" class="site-header {{ $headerLayout }}">
    <div class="print-only container">
        <div class="grid">
            <div class="grid-sm-12">
                {!! municipio_get_logotype('standard') !!}
            </div>
        </div>
    </div>

    @if ($headerLayout == 'header-jumbo')
        @include('partials.header.jumbo')
    @else
        @include('partials.header.default')
    @endif
</header>

@include('partials.hero')
