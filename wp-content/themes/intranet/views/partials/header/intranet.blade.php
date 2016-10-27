<nav class="navbar navbar-sm hidden-print">
    <div class="container">
        <div class="grid grid-table">
            <div class="grid-fit-content">
                {!! municipio_intranet_get_logotype('negative', true) !!}
            </div>
            <div class="grid-auto">
                <div class="grid">
                    <div class="grid-md-6">
                        <span class="h3 site-title">Helsingborgs stads intranät</span>
                    </div>
                    <div class="grid-md-6 text-right">
                        @include('partials.header.subnav')
                    </div>
                </div>
                <div class="grid grid-table grid-va-middle">
                    <div class="grid-auto no-padding">
                        @include('partials.header.network-selector')
                    </div>
                    <div class="grid-fit-content">
                        <span class="or"><?php _e('or', 'municipio-intranet'); ?></span>
                    </div>
                    <div class="grid-auto no-padding">
                        @include('partials.header.search')
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
