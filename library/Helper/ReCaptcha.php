<?php

namespace Municipio\Helper;

class ReCaptcha
{
    /**
     * Enqueue Google reCAPTCHA
     * @return void
     */
    public static function enqueueReCaptcha()
    {
        if (defined('G_RECAPTCHA_KEY') && defined('G_RECAPTCHA_SECRET')) {

            wp_enqueue_script('municipio-google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . G_RECAPTCHA_KEY);
            wp_add_inline_script('municipio-google-recaptcha', "
            
                  var interval = setInterval(function(){
                  if(window.grecaptcha){
                        grecaptcha.ready(function() {
                            grecaptcha.execute('" . G_RECAPTCHA_KEY . "', {action: 'homepage'}).then(function(token) {
                                document.getElementById('g-recaptcha-response').value = token;
                            });
                        });
                    clearInterval(interval);
                  }
                }, 100);
            
            ", 'after');
        }
    }

    /**
     * Check if Google reCaptcha request is valid
     * @param $response
     * @return bool
     */
    public static function controlReCaptcha($response): bool
    {
        if (defined('G_RECAPTCHA_SECRET') && $response) {

            $verify = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . G_RECAPTCHA_SECRET
                . '&response=' . $response);
            $recaptcha = json_decode($verify["body"]);

            if ($recaptcha->score >= 0.5) {
                return $recaptcha->success;
            }
        }
    }

    /**
     * Add admin notice if Google reCaptcha constants is missing
     */
    public static function recaptchaConstants()
    {
        if (defined('G_RECAPTCHA_KEY') && defined('G_RECAPTCHA_SECRET')) {
            return;
        }

        $class = 'c-notice c-notice--warning';
        $message = __('Municipio: constant \'G_RECAPTCHA_KEY\' or \'G_RECAPTCHA_SECRET\' is not defined.', 'municipio');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    /**
     * Check reCaptcha before comment is saved to post
     * @return void
     */
    public static function validateReCaptcha()
    {
        $response = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';
        $reCaptcha = \Municipio\Helper\ReCaptcha::controlReCaptcha($response);

        if (!$reCaptcha) {
            wp_die(sprintf('<strong>%s</strong>:&nbsp;%s', __('Error', 'municipio'),
                __('reCaptcha verification failed', 'municipio')));
        }
    }
}
