<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ get_bloginfo('name') }}</title>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true" />

    <meta name="google-translate-customization" content="10edc883cb199c91-cbfc59690263b16d-gf15574b8983c6459-12">

    <script>
        var ajaxurl = '{!! admin_url('admin-ajax.php') !!}';
    </script>

    <!--[if lt IE 9]>
    <script type="text/javascript">
        document.createElement('header');
        document.createElement('nav');
        document.createElement('section');
        document.createElement('article');
        document.createElement('aside');
        document.createElement('footer');
        document.createElement('hgroup');
    </script>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <![endif]-->

    {!! wp_head() !!}
</head>
<body {!! body_class() !!}>
    <!--[if lt IE 9]>
        <div class="notice info browserupgrade">
            <div class="container"><div class="grid-table grid-va-middle">
                <div class="grid-col-icon">
                    <i class="fa fa-plug"></i>
                </div>
                <div class="grid-sm-12">
                Du använder en gammal webbläsare. För att hemsidan ska fungera så bra som möjligt bör du byta till en modernare webbläsare. På <a href="http://browsehappy.com">browsehappy.com</a> kan du få hjälp att hitta en ny modern webbläsare.
                </div>
            </div></div>
        </div>
    <![endif]-->

    <div id="wrapper">
        @include('partials.stripe')

        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                   <span class="h1 no-margin no-padding">Helsingborgs stads intranät</span>
                </div>
            </div>
        </div>

        <div class="container main-container">
            <div class="grid">
                <div class="grid-lg-6 grid-md-8 grid-sm-12">
                    <h1>404 <em>{{ get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_message') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_message') : 'The page could not be found' }}</em></h1>

                    <ul class="actions">
                        @if (is_array(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')) && in_array('search', get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')))
                        <li>
                            <a rel="nofollow" href="{{ home_url() }}?s={{ $keyword }}" class="link-item link-item-light">{{ sprintf(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_search_link_text') : 'Search "%s"', $keyword) }}</a>
                        </li>
                        @endif

                        @if (is_array(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')) && in_array('home', get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')))
                        <li><a href="{{ home_url() }}" class="link-item link-item-light">{{ get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_home_link_text') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_home_link_text') : 'Go to home' }}</a></li>
                        @endif
                    </ul>

                    {!! get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_info') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_info') : '' !!}

                    @if (is_array(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')) && in_array('back', get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')))
                    <p>
                        <a href="javascript:history.go(-1);" class="btn btn-primary">
                            <i class="fa fa-arrow-circle-o-left"></i>
                            {{ get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_back_button_text') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_back_button_text') : 'Go back' }}
                        </a>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! wp_footer() !!}

</body>
</html>
