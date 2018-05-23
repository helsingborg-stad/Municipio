@include('partials.navigation.search-top')

@if (isset($headerLayout['headers']) && is_array($headerLayout['headers']) && !empty($headerLayout['headers']))
    <header class="s-site-header" id="site-header">
        @foreach ($headerLayout['headers'] as $header)
            @if ($header->sidebar)
                <div {!! $header->wrapper !!}>
                    <div {!! $header->container !!}>
                        <?php dynamic_sidebar($header->sidebar); ?>
                    </div>
                </div>
            @endif
        @endforeach

        <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
            @include('partials.mobile-menu')
        </nav>
    </header>
@endif

<?php dynamic_sidebar('top-sidebar'); ?>
