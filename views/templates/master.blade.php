<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo apply_filters('Municipio/pageTitle', wp_title('|', false, 'right')); ?></title>

    <meta name="pubdate" content="{{ the_time('Y-m-d') }}">
    <meta name="moddate" content="{{ the_modified_time('Y-m-d') }}">

    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true"/>

    <script>
        var ajaxurl = '{!! apply_filters('Municipio/ajax_url_in_head', admin_url('admin-ajax.php')) !!}';
    </script>

    {!! wp_head() !!}
</head>
<body {!! body_class('no-js') !!}>

<a href="#main-menu" class="btn btn-default btn-block btn-lg btn-offcanvas"
   tabindex="1"><?php _e('Jump to the main menu', 'municipio'); ?></a>
<a href="#main-content" class="btn btn-default btn-block btn-lg btn-offcanvas"
   tabindex="2"><?php _e('Jump to the main content', 'municipio'); ?></a>

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

    @if ($translateLocation == 'header')
        @include('partials.translate')
    @endif

    @include('partials.header')

    <main id="main-content" class="clearfix main-content">
        @yield('content')

        @if (is_active_sidebar('content-area-bottom'))
            <div class="container u-py-5 sidebar-content-area-bottom">
                <div class="grid grid--columns">
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

    @if (in_array($translateLocation, array('footer', 'fold')))
        @include('partials.translate')
    @endif

</div>

{!! wp_footer() !!}

<script type="application/javascript">



    function googleTranslateElementInit() {

        new google.translate.TranslateElement(
            {
                pageLanguage: 'sv',
                autoDisplay: false,
                gaTrack: HbgPrimeArgs.googleTranslate.gaTrack,
                gaId: HbgPrimeArgs.googleTranslate.gaUA,
            },
            'google-translate-element'
        );


    }

</script>

</body>
</html>
