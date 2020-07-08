<!DOCTYPE html>
<html {!! $languageAttributes !!}>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $pageTitle }}</title>

    <meta name="pubdate" content="{{ $pagePublished }}">
    <meta name="moddate" content="{{ $pageModified }}">

    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true"/>

    <script>
        var ajaxurl = '{!! $ajaxUrl !!}';
    </script>

    {{-- Wordpress required call to wp_header() --}}
    {!! $wpHeader !!}

</head>

<body class="{{ $bodyClass }}">

    {{-- Site header --}}
    @includeIf('partials.header')

    <div class="">

        {{-- Notices Notice::add() --}}
        @if($notice) 
            @foreach ($notice as $noticeItem)
                @notice($noticeItem)
                @endnotice
            @endforeach
        @endif

        {{-- Before page layout --}}
        @yield('before-layout')

        {{-- Page layout --}}
        @section('layout')
            <main>
                <div class="o-container">
                    <div class="o-row">
                        <div class="o-col-12">
                            @yield('above')
                        </div>
                    </div>
                    <div class="o-row">
                        <div class="o-col-12 o-col-3@md o-col-order-2 o-col-order-1@md">
                            @yield('sidebar-left')
                        </div>
                        <div class="o-col-12 o-col-6@md o-col-order-1 o-col-order-2@md">
                            @yield('content')
                        </div>
                        <div class="o-col-12 o-col-3@md o-col-order-3 o-col-order-3@md">
                            @yield('sidebar-right')
                        </div>
                    </div>
                    <div class="o-row">
                        <div class="o-col-12">
                            @yield('below')
                        </div>
                    </div>
                </div>
            </main>
        @show

        {{-- After page layout --}}
        @yield('after-layout')

    </div>
</div>

@section('footer')
    @includeIf('partials.footer')
@show

{{-- Wordpress required call to wp_footer() --}}
{!! $wpFooter !!}

</body>
</html>