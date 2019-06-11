<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo apply_filters('Municipio/pageTitle', wp_title('|', false, 'right')); ?></title>

    <meta name="pubdate" content="{{ the_time('Y-m-d') }}">
    <meta name="moddate" content="{{ the_modified_time('Y-m-d') }}">

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true" />

    <script>
        var ajaxurl = '{!! apply_filters('Municipio/ajax_url_in_head', admin_url('admin-ajax.php')) !!}';
    </script>

    {!! wp_head() !!}
</head>
<body {!! body_class('no-js') !!}>



<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInitPageLoad"></script>

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

        @if ($translateLocation == 'header')
            @include('partials.translate')
        @endif

        @include('partials.header')


        <style>#google_translate_element_page_load,.skiptranslate{display:none;}body{top:0!important;}</style>
        <div id="google_translate_element_page_load"></div>
        
        <script>
            function googleTranslateElementInitPageLoad() {
                new google.translate.TranslateElement({
                    pageLanguage: 'sv', 
                    includedLanguages: 'et', 
                    autoDisplay: false
                }, 'google_translate_element_page_load');
                var a = document.querySelector("#google_translate_element_page_load select");
                a.selectedIndex=1;
                a.dispatchEvent(new Event('change'));
            }
        </script>

        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInitPageLoad"></script>

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

</body>
</html>
