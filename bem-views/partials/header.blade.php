@include('partials.navigation.search-top')

@if (isset($headerLayout['bars']) && is_array($headerLayout['bars']) && !empty($headerLayout['bars']))
    <header class="s-site-header" id="site-header">
        @foreach ($headerLayout['bars'] as $header)
            <div {!! $header['attributes'] !!}>
                <div class="{{$header['container']}}">
                    <div class="c-navbar__body">
                        <?php dynamic_sidebar($header['sidebar']); ?>
                    </div>
                </div>
            </div>
        @endforeach

        <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
            @include('partials.mobile-menu')
        </nav>
    </header>
@endif

@if (isset($headerLayout['headers']) && is_array($headerLayout['headers']) && !empty($headerLayout['headers']))

    <header id="header" class="c-site-header">

        @foreach ($headerLayout['headers'] as $header)
            <div class="{{$header['class']}}">
                @if (isset($header['items']) && !empty($header['items']))
                    <div class="{{$header['rowClass']}}">

                        @foreach ($header['items'] as $item)
                            <div class="{{$item['class']}}">
                                <?php dynamic_sidebar($item['id']); ?>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>
        @endforeach

        <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
            @include('partials.mobile-menu')
        </nav>

    </header>

@elseif($showAdminNotices === true)
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                @include('partials.notice',
                    ['notice' =>
                        ['class' => "info theme-admin-warning",
                        'icon' => "pricon pricon-info-o",
                        'text' => "You have not configured any header. You can add a header in the customizer."]
                    ]
                )
            </div>
        </div>
    </div>
@endif

@include('partials.hero')
