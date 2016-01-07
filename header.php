<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Helsingborg stad</title>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="pubdate" content="<?php echo the_time('d M Y'); ?>">
    <meta name="moddate" content="<?php echo the_modified_time('d M Y'); ?>">

    <meta name="google-translate-customization" content="10edc883cb199c91-cbfc59690263b16d-gf15574b8983c6459-12">

    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/favicon.ico" type="image/x-icon">

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-touch-icon-144x144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo get_template_directory_uri(); ?>/assets/images/icons/apple-touch-icon-precomposed.png">

    <script>
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
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
            <div class="grid-lg-12">
                <a href="<?php echo home_url(); ?>" class="logotype">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/dist/images/helsingborg.svg" alt="Helsingborg Stad" width="239" height="68">
                </a>

                <nav class="pull-right clearfix">
                    <ul class="nav nav-tabs">
                        <li><a href="#">Besökare</a></li>
                        <li><a href="#">Företagare</a></li>
                        <li><a href="#">Självservice</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
