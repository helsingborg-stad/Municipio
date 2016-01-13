<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Helsingborg stad</title>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="pubdate" content="{{ the_time('d M Y') }}">
    <meta name="moddate" content="{{ the_modified_time('d M Y') }}">

    <meta name="google-translate-customization" content="10edc883cb199c91-cbfc59690263b16d-gf15574b8983c6459-12">

    <link rel="icon" href="{{ get_template_directory_uri() }}/assets/images/icons/favicon.ico" type="image/x-icon">

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ get_template_directory_uri() }}/assets/images/icons/apple-touch-icon-144x144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ get_template_directory_uri() }}/assets/images/icons/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ get_template_directory_uri() }}/assets/images/icons/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="{{ get_template_directory_uri() }}/assets/images/icons/apple-touch-icon-precomposed.png">

    <script>
        var ajaxurl = '{{ admin_url('admin-ajax.php') }}';
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

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="wrapper">

<header id="site-header">
    <div class="container">
        <div class="grid">
            <div class="grid-md-6 text-center-xs text-center-sm">
                <a href="<?php echo home_url(); ?>" class="logotype" data-tooltip="Gå till startsidan">
                    <img src="{{ get_template_directory_uri() }}/assets/dist/images/helsingborg.svg" alt="Helsingborg Stad" width="239" height="68">
                </a>
            </div>
            <div class="grid-md-6 text-center-sm text-center-xs text-right">
                <nav>
                    <ul class="nav nav-tabs">
                        <li><a href="#">Besökare</a></li>
                        <li><a href="#">Företagare</a></li>
                        <li><a href="#">Självservice</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-mainmenu">
        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                    <ul class="nav nav-justify">
                        <li><a href="#">Arbete</a></li>
                        <li><a href="#">Bo, bygga och miljö</a></li>
                        <li><a href="#">Förskola och utbildning</a></li>
                        <li><a href="#">Kommun och politik</a></li>
                        <li><a href="#">Omsorg och stöd</a></li>
                        <li><a href="#">Trafik och stadsplanering</a></li>
                        <li><a href="#">Uppleva och göra</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    @if (is_front_page())
        @include('templates.partials.hero')
    @endif
</header>
