<?php

namespace Municipio\Theme;

use \Municipio\Helper\Styleguide;

class Enqueue
{
    public $defaultPrimeName = 'hbg-prime';

    public function __construct()
    {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'style'), 5);
        add_action('wp_enqueue_scripts', array($this, 'script'), 5);

        // Admin style
        add_action('admin_enqueue_scripts', array($this, 'adminStyle'), 999);

        //Google scripts
        add_action('wp_enqueue_scripts', array($this, 'googleTagManager'), 999);
        add_action('wp_enqueue_scripts', array($this, 'googleReCaptcha'), 999);
        add_action('wp_footer', array($this, 'addGoogleTranslate'), 999);

        // Removes version querystring from scripts and styles
        add_filter('script_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        add_filter('style_loader_src', array($this, 'removeScriptVersion'), 15, 1);

        // Removes generator tag
        add_filter('the_generator', function ($a, $b) {
            return '';
        }, 9, 2);

        //Move scripts to footer
        add_action('wp_print_scripts', array($this, 'moveScriptsToFooter'));

        //Enable defered loading
        add_filter('script_loader_tag', array($this, 'deferedLoadingJavascript'), 10, 2);

        // Plugin filters (script/style related)
        add_filter('gform_init_scripts_footer', '__return_true');
        add_filter('gform_cdata_open', array($this, 'wrapGformCdataOpen'));
        add_filter('gform_cdata_close', array($this, 'wrapGformCdataClose'));
    }


    public function wrapGformCdataOpen($content)
    {
        $content = 'document.addEventListener( "DOMContentLoaded", function() { ';
        return $content;
    }

    public function wrapGformCdataClose($content)
    {
        $content = ' }, false );';
        return $content;
    }

    /**
     * Enqueue admin style
     * @return void
     */
    public function adminStyle()
    {

    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style()
    {
        // Load material icons
        wp_register_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
        wp_enqueue_style('material-icons');

        // Load styleguide css
        wp_register_style('styleguide-css', get_template_directory_uri(). '/assets/dist/'
            . \Municipio\Helper\CacheBust::name('css/styleguide.css'));
        wp_enqueue_style('styleguide-css');

        // Load local municipio css
        wp_register_style('municipio-css', get_template_directory_uri(). '/assets/dist/'
            . \Municipio\Helper\CacheBust::name('css/municipio.css'));
        wp_enqueue_style('municipio-css');
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function script()
    {

        // Language & parameters 
        wp_localize_script('municipio', 'MunicipioLang', array(
            'printbreak' => array(
                'tooltip' => __('Insert Print Page Break tag', 'municipio')
            ),
            'messages' => array(
                'deleteComment' => __('Are you sure you want to delete the comment?', 'municipio'),
                'onError' => __('Something went wrong, please try again later', 'municipio'),
            )
        ));

        //Enqueue polyfills
        wp_enqueue_script('polyfill', 'https://cdn.polyfill.io/v3/polyfill.min.js', 'municipio');

        //Comment reply
        if (is_singular() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        //Instant page load
        if (!defined('INSTANT_PAGE_DISABLED')) {
            wp_enqueue_script('instant-page', 'https://instant.page/3.0.0', array(), '', true);
        }

        //Load local styleguide js
        wp_register_script('styleguide-js', get_template_directory_uri(). '/assets/dist/'
            . \Municipio\Helper\CacheBust::name('js/styleguide.js'));
        wp_enqueue_script('styleguide-js');

        //Load local municipio js
        wp_register_script('municipio-js', get_template_directory_uri(). '/assets/dist/'
            . \Municipio\Helper\CacheBust::name('js/municipio.js'));
        wp_enqueue_script('municipio-js');
    }

    /**
     * Move all scripts to footer, discard settings. 
     * 
     * @return void
     */
    public function moveScriptsToFooter()
    {
        global $wp_scripts;
        $notInFooter = array_diff($wp_scripts->queue, $wp_scripts->in_footer);
        $wp_scripts->in_footer = array_merge($wp_scripts->in_footer, $notInFooter);
    }

    /**
     * Enqueue Google reCAPTCHA
     * 
     * @return void
     */
    public function googleReCaptcha()
    {
        if (defined('G_RECAPTCHA_KEY') && defined('G_RECAPTCHA_SECRET')) {
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit', '', '1.0.0', true);
            wp_add_inline_script('google-recaptcha', '
            var CaptchaCallback = function() {
                jQuery(\'.g-recaptcha\').each(function(index, el) {
                    grecaptcha.render(el, {\'sitekey\' : \'' . G_RECAPTCHA_KEY . '\'});
                });
            };', 'before');
        }
    }

    /**
     * Enqueues Google Tag Manager
     * 
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
     * Print the google translate element
     * 
     * @return void
     */
    public function addGoogleTranslate()
    {
        echo "<script>
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
        </script>";
    }


    /**
     * Removes querystring from any scripts/styles internally
     * 
     * @param  string $src The soruce path
     * 
     * @return string      The source path without any querystring
     */
    public function removeScriptVersion($src)
    {
        $siteUrlComponents  = parse_url(get_site_url());
        $urlComponents      = parse_url($src);

        // Check if the URL is internal or external
        if (!empty($siteUrlComponents['host'])
            && !empty($urlComponents['host'])
            && strcasecmp($urlComponents['host'], $siteUrlComponents['host']) === 0
            && !is_admin_bar_showing()) {
            $src = !empty($urlComponents['query']) ? str_replace('?' . $urlComponents['query'], '', $src) : $src;
            return $src;
        } else {
            return $src;
        }
    }

    /**
     * Making deffered loading of scripts a posibillity (removes unwanted renderblocking js)
     * 
     * @param  string $tag    HTML Script tag
     * @param  string $handle Script handle
     * 
     * @return string         The script tag
     */
    public function deferedLoadingJavascript($tag, $handle)
    {
        if (is_admin()) {
            return $tag;
        }

        if (isset($_GET['preview']) && $_GET['preview'] == 'true') {
            return $tag;
        }

        $scriptsHandlesToIgnore = apply_filters('Municipio/Theme/Enqueue/deferedLoadingJavascript/handlesToIgnore', array('readspeaker', 'jquery-core', 'jquery-migrate'), $handle);
        $disableDeferedLoading = apply_filters('Municipio/Theme/Enqueue/deferedLoadingJavascript/disableDeferedLoading', false);

        if (in_array($handle, $scriptsHandlesToIgnore) || $disableDeferedLoading) {
            return $tag;
        }

        return str_replace(' src', ' defer="defer" src', $tag);
    }
}
