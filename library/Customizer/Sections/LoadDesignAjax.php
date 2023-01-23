<?php

namespace Municipio\Customizer\Sections;

class LoadDesignAjax extends \Municipio\Helper\Ajax
{
    public function __construct()
    {
        //Localize
        $this->localize('designShare', 'design-share-js');

        //Hook method to ajax
        $this->hook('ajaxSaveFontFile', false);
    }
    public function ajaxSaveFontFile()
    {
        if (!defined('DOING_AJAX') && !DOING_AJAX) {
            return false;
        }

        if (!wp_verify_nonce($_POST['nonce'], 'designShareNonce')) {
            die('Couldn\'t verify nonce.');
        }

        $fontUrl = $_REQUEST['fontUrl'];
        $localFontFileExists = (bool) \Municipio\Helper\File::getFileUrl(basename($fontUrl));
        if (!$localFontFileExists) {
            // TODO Sideload font file
            // $_REQUEST['fontLabel']
        }

        \var_dump($localFontFileExists);
        wp_die();
    }
}
