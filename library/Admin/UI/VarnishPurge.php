<?php

namespace Municipio\Admin\UI;

class VarnishPurge
{
    public function __construct()
    {
        add_action('admin_bar_menu', array($this, 'varnishAdminbar'), 100);
        add_action('wp', array($this, 'purgeUrl'));
        add_action('admin_init', array($this, 'purgeUrl'));

        add_filter('load_textdomain_mofile', array($this, 'varnishTranslation'), 10, 2);
    }

    public function purgeUrl()
    {
        if (!current_user_can('edit_post')) {
            return;
        }

        if (isset($_GET['vhp_flush_url']) && check_admin_referer('varnish-http-purge')) {
            global $post;
            global $purger;

            if (!isset($post) && isset($_GET['post']) && $_GET['post'] > 0) {
                $post = get_post($_GET['post']);
            }

            if (!isset($post) || !is_object($post) || !isset($purger)) {
                return;
            }

            $purger->purgeUrl(get_permalink($post->ID));
            add_action('admin_notices', function () {
                echo "<div id='message' class='updated fade'><p><strong>".__('Varnish cache purged for this post!', 'municipio')."</strong></p></div>";
            });

            \Municipio\Helper\Notice::add('Page purged!', 'success');
        }
    }

    public function varnishAdminbar($adminBar)
    {
        if (!current_user_can('edit_post')) {
            return;
        }

        global $post;
        global $purger;

        if (!isset($post) || !is_object($post) || !isset($purger)) {
            return;
        }

        $adminBar->add_menu(array(
            'id'    => 'purge-varnish-cache-url',
            'title' => __('Purge Page', 'municipio'),
            'href'  => wp_nonce_url(add_query_arg('vhp_flush_url', 1), 'varnish-http-purge')
        ));
    }

    public function varnishTranslation($mofile, $domain)
    {
        if ('varnish-http-purge' == $domain) {
            if (file_exists(WP_LANG_DIR . '/plugins/' . basename($mofile))) {
                $mofile = WP_LANG_DIR . '/plugins/' . basename($mofile);
            }
        }

        return $mofile;
    }
}
