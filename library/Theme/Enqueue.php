<?php

namespace Municipio\Theme;

class Enqueue
{
    public function __construct()
    {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'style'), 5);
        add_action('wp_enqueue_scripts', array($this, 'script'), 5);

        // Admin style
        add_action('admin_enqueue_scripts', array($this, 'adminStyle'), 999);

        add_action('wp_enqueue_scripts', array($this, 'googleAnalytics'), 999);
        add_action('wp_enqueue_scripts', array($this, 'googleTagManager'), 999);

        // Removes version querystring from scripts and styles
        add_filter('script_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        add_filter('style_loader_src', array($this, 'removeScriptVersion'), 15, 1);

        // Removes generator tag
        add_filter('the_generator', create_function('', 'return "";'));

        //Move scripts to footer
        add_action('wp_print_scripts', array($this, 'moveScriptsToFooter'));

        //Enable defered loading
        add_action('clean_url', array($this, 'deferedLoadingJavascript'));
    }

    /**
     * Enqueue admin style
     * @return void
     */
    public function adminStyle()
    {
        wp_register_style('helsingborg-se-admin', get_template_directory_uri() . '/assets/dist/css/admin.min.css', '', @filemtime(get_template_directory_uri() . '/assets/dist/css/admin.min.css'));
        wp_enqueue_style('helsingborg-se-admin');

        wp_register_script('helsingborg-se-admin', get_template_directory_uri() . '/assets/dist/js/admin.min.js', '', @filemtime(get_template_directory_uri() . '/assets/dist/js/admin.min.js'), true);
        wp_enqueue_script('helsingborg-se-admin');
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style()
    {
        $loadWpJquery = apply_filters('Municipio/load-wp-jquery', false);

        if (!$loadWpJquery) {
            wp_deregister_script('jquery');
        }

        if ((defined('DEV_MODE') && DEV_MODE === true) || (isset($_GET['DEV_MODE']) && $_GET['DEV_MODE'] === 'true')) {
            wp_register_style('hbg-prime', '//hbgprime.dev/dist/css/hbg-prime.min.css', '', '1.0.0');
        } else {
            wp_register_style('hbg-prime', '//helsingborg-stad.github.io/styleguide-web-cdn/styleguide.dev/dist/css/hbg-prime.min.css', '', '1.0.0');
        }

        wp_enqueue_style('hbg-prime');

        if (is_readable(get_template_directory_uri() . '/assets/dist/css/app.min.css')) {
            wp_register_style('municipio', get_template_directory_uri() . '/assets/dist/css/app.min.css', '', @filemtime(get_template_directory_uri() . '/assets/dist/css/app.min.css'));
        } else {
            wp_register_style('municipio', get_template_directory_uri() . '/assets/dist/css/app.min.css', '', '1.1.1');
        }

        wp_enqueue_style('municipio');
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function script()
    {
        //Remove jQuery
        if (!is_admin()) {
            wp_dequeue_script('jquery');
        }

        //Custom
        if (defined('DEV_MODE') && DEV_MODE === true) {
            wp_register_script('hbg-prime', '//hbgprime.dev/dist/js/hbg-prime.min.js', '', '1.0.0', true);
        } else {
            wp_register_script('hbg-prime', '//helsingborg-stad.github.io/styleguide-web-cdn/styleguide.dev/dist/js/hbg-prime.min.js', '', '1.0.0', true);
        }

        //Localization
        wp_localize_script('hbg-prime', 'HbgPrimeArgs', array(
            'cookieConsent' => array(
                'show'      => get_field('cookie_consent_active', 'option'),
                'message'   => get_field('cookie_consent_message', 'option'),
                'button'    => get_field('cookie_consent_button', 'option'),
                'placement' => get_field('cookie_consent_placement', 'option')
            ),
            'googleTranslate' => array(
                'gaTrack' => get_field('google_translate_ga_track', 'option'),
                'gaUA'    => get_field('google_analytics_ua', 'option')
            )
        ));
        wp_enqueue_script('hbg-prime');

        wp_register_script('municipio', get_template_directory_uri() . '/assets/dist/js/packaged.min.js', '', '1.0.0', true);
        wp_localize_script('municipio', 'MunicipioLang', array(
            'printbreak' => array(
                'tooltip' => __('Insert Print Page Break tag', 'municipio')
            )
        ));
        wp_enqueue_script('municipio');
    }

    public function moveScriptsToFooter()
    {
        global $wp_scripts;
        $notInFooter = array_diff($wp_scripts->queue, $wp_scripts->in_footer);
        $wp_scripts->in_footer = array_merge($wp_scripts->in_footer, $notInFooter);
    }

    /**
     * Enqueue Google Analytics
     * @return void
     */
    public function googleAnalytics()
    {
        $gaUser = get_field('google_analytics_ua', 'option');

        if (empty($gaUser)) {
            return;
        }

        wp_register_script('google-analytics', 'https://www.google-analytics.com/analytics.js', '', '1.0.0', true);
        wp_enqueue_script('google-analytics');

        add_filter('script_loader_tag', function ($tag, $handle) use ($gaUser) {
            if ($handle != 'google-analytics') {
                return $tag;
            }

            $ga = "<script>
                        window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
                        ga('create','" . $gaUser . "','auto');ga('send','pageview')
                    </script>";

            return $ga . str_replace(' src', ' async defer src', $tag);
        }, 10, 2);
    }

    /**
     * Enqueues Google Tag Manager
     * @return void
     */
    public function googleTagManager()
    {
        $user = get_field('google_tag_manager_id', 'option');

        if (empty($user)) {
            return;
        }

        add_action('wp_footer', function () use ($user) {
            echo "<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id={$user}\"
                    height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
                <script>
                    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','{$user}');
                </script>";
        }, 999);
    }

    /**
     * Removes querystring from any scripts/styles loaded from "helsingborg" or "localhost"
     * @param  string $src The soruce path
     * @return string      The source path without any querystring
     */
    public function removeScriptVersion($src)
    {
        $parts = explode('?', $src);
        if (strpos($parts[0], 'helsingborg') > -1 || strpos($parts[0], 'localhost') > -1) {
            return $parts[0];
        } else {
            return $src;
        }
    }

    /**
     * Making deffered loading of scripts a posibillity (remnoves unwanted renderblocking js)
     * @param  string $src The soruce path
     * @return string      The source path without any querystring
     */
    public function deferedLoadingJavascript($url)
    {
        if (is_admin() || false !== strpos($url, 'readspeaker.com') || false === strpos($url, '.js')) {
            return $url;
        }

        return $url . "' defer='defer";
    }
}
