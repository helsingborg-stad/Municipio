<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE">
    <meta charset="utf-8">

    <title><?php echo apply_filters('Municipio/pageTitle', wp_title('|', false, 'right') . get_bloginfo('name')); ?></title>

    <meta name="description" content="{{ bloginfo('description') }}" />
    <meta name="pubdate" content="{{ the_time('Y-m-d') }}">
    <meta name="moddate" content="{{ the_modified_time('Y-m-d') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true" />

    <script>
        var ajaxurl = '{!! apply_filters('Municipio/ajax_url_in_head', admin_url('admin-ajax.php')) !!}';
    </script>

    {!! wp_head() !!}
</head>
<body {!! body_class('no-js') !!}>
    <div id="wrapper">
        @include('partials.stripe')

        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                    {!! municipio_get_logotype(!empty(get_field('404_error_logotype', 'options')) ? get_field('404_error_logotype', 'options') : 'standard')  !!}
                </div>
            </div>
        </div>
        @yield('content')
     </div>

    {!! wp_footer() !!}
</body>
</html>
