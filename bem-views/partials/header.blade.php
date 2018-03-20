@include('partials.navigation.search-top')

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
