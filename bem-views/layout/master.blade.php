<!DOCTYPE html>
<html {!! $languageAttributes !!}>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">
    <meta charset="utf-8">

    <title>{{$wpTitle}}</title>

    <meta name="description" content="{{ $description }}" />
    <meta name="pubdate" content="{{ $published }}">
    <meta name="moddate" content="{{ $modified }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true" />

    <script> var ajaxurl = '{!! $ajaxUrl !!}';</script>

    {!! wp_head() !!}

</head>
<body class="{{ $bodyClass }}">

    <a href="#main-menu" class="btn btn-default btn-block btn-lg btn-offcanvas" tabindex="1"><?php _e('Jump to the main menu', 'municipio'); ?></a>
    <a href="#main-content" class="btn btn-default btn-block btn-lg btn-offcanvas" tabindex="2"><?php _e('Jump to the main content', 'municipio'); ?></a>

    <div id="wrapper">
        @if (isset($notice) && !empty($notice))
            <div class="notices">
            @if (!isset($notice['text']) && is_array($notice))
                @foreach ($notice as $notice)
                    @include('partials.notice')
                @endforeach
            @else
                @include('partials.notice')
            @endif
            </div>
        @endif

        @if (get_field('show_google_translate', 'option') == 'header')
            @include('partials.translate')
        @endif

        @include('partials.header')

        <main id="main-content" class="clearfix main-content">
            @yield('content')

            @if (is_active_sidebar('content-area-bottom'))
            <div class="container gutter-xl gutter-vertical sidebar-content-area-bottom">
                <div class="grid">
                    <?php dynamic_sidebar('content-area-bottom'); ?>
                </div>
            </div>
            @endif
        </main>

        @include('partials.footer')

        @if (isset($fab['menu']))
            @include('partials.fixed-action-bar')
        @endif

        @include('partials.vertical-menu')

        @if (in_array(get_field('show_google_translate', 'option'), array('footer', 'fold')))
            @include('partials.translate')
        @endif
     </div>

    {!! wp_footer() !!}

</body>
</html>
