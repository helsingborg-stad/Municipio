<?php

namespace Municipio\Theme;

class Enqueue
{
    public function __construct()
    {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'style'));
        add_action('wp_enqueue_scripts', array($this, 'script'));

        // Admin style
        add_action('admin_enqueue_scripts', array($this, 'adminStyle'), 999);

        add_action('wp_enqueue_scripts', array($this, 'googleAnalytics'), 999);

        // Removes version querystring from scripts and styles
        add_filter('script_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        add_filter('style_loader_src', array($this, 'removeScriptVersion'), 15, 1);

        // Removes generator tag
        add_filter('the_generator', create_function('', 'return "";'));
    }

    public function adminStyle()
    {
        wp_register_style('helsingborg-se-admin', get_template_directory_uri() . '/assets/dist/css/admin.min.css', '', filemtime(get_stylesheet_directory() . '/assets/dist/css/admin.min.css'));
        wp_enqueue_style('helsingborg-se-admin');
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style()
    {
        if (defined('DEV_MODE') && DEV_MODE === true) {
            wp_register_style('hbg-prime', 'http://hbgprime.dev/dist/css/hbg-prime.min.css', '', '1.0.0');
        } else {
            wp_register_style('hbg-prime', 'http://helsingborg-stad.github.io/styleguide-web-cdn/styleguide.dev/dist/css/hbg-prime.min.css', '', '1.0.0');
        }

        wp_enqueue_style('hbg-prime');

        wp_register_style('helsingborg-se', get_template_directory_uri() . '/assets/dist/css/app.min.css', '', filemtime(get_stylesheet_directory() . '/assets/dist/css/app.min.css'));
        wp_enqueue_style('helsingborg-se');
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function script()
    {
        //Bundled
        wp_enqueue_script("jquery");

        //Custom
        if (defined('DEV_MODE') && DEV_MODE === true) {
            wp_register_script('hbg-prime', 'http://hbgprime.dev/dist/js/hbg-prime.min.js', '', '1.0.0', true);
        } else {
            wp_register_script('hbg-prime', 'http://helsingborg-stad.github.io/styleguide-web-cdn/styleguide.dev/dist/js/hbg-prime.min.js', '', '1.0.0', true);
        }

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

        wp_register_script('bootstrap-theme', get_template_directory_uri() . '/assets/dist/js/packaged.min.js');
        wp_enqueue_script('bootstrap-theme');

        if (get_field('show_google_translate', 'option') !== false && get_field('show_google_translate', 'option') != 'false') {
            wp_register_script('google-translate', '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit', '', '1.0.0', true);
            wp_enqueue_script('google-translate');
        }
    }

    /**
     * Enqueue Google Analytics
     * @return void
     */
    public function googleAnalytics()
    {
        $gaUser = get_field('google_analytics_ua', 'option');

        if (!$gaUser) {
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
}
