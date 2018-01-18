<?php

namespace Municipio\Theme;

class Archive
{
    public function __construct()
    {
        add_filter('wp_title', array($this, 'pageTitle'));
        add_filter('get_the_archive_title', array($this, 'pageHeader'));
        add_action('pre_get_posts', array($this, 'onlyFirstLevel'));
        add_action('pre_get_posts', array($this, 'enablePageForPostTypeChildren'));
        add_action('wp_ajax_share_email', array($this, 'socialShareEmail'));
        add_action('wp_ajax_nopriv_share_email', array($this, 'socialShareEmail'));
        add_action('pre_get_posts', array($this, 'filterNumberOfPostsInArchive'), 1, 1);
    }

    /**
     * Function to set number of posts in display list.
     * @param $query The wp-query for archive
     */
    public function filterNumberOfPostsInArchive($query)
    {
        if (!is_admin() && $query->is_main_query()) {
            if (isset($query->query["post_type"]) && post_type_exists($query->query["post_type"])) {
                $postCount = get_field('archive_' . $query->query["post_type"] . '_number_of_posts', 'option');

                if (!empty($postCount) && is_numeric($postCount)) {
                    $query->set('posts_per_page', $postCount);
                }
            }
        }
    }

    /**
     * Filter away "Archive:" etc from pageHeader
     * @param  string $title
     * @return string
     */
    public function pageHeader($title)
    {
        if (is_category()) {
            return single_cat_title('', false);
        } elseif (is_tag()) {
            return single_cat_title('', false);
        } elseif (is_author()) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';
        } elseif (is_year()) {
            return get_the_date(_x('Y', 'yearly archives date format'));
        } elseif (is_month()) {
            return get_the_date(_x('F Y', 'monthly archives date format'));
        } elseif (is_day()) {
            return get_the_date(_x('F j, Y', 'daily archives date format'));
        } elseif (is_post_type_archive()) {
            return post_type_archive_title('', false);
        }
        return $title;
    }

    /**
     * Filter away "Archive:" from archive title
     * @param  string $title
     * @return string
     */
    public function pageTitle($title)
    {
        return preg_replace('/(archive|arkiv|' . __('Archive') . '):/i', '', $title);
    }

    public function onlyFirstLevel($query)
    {
        if (is_author() || !is_archive() || !$query->is_main_query() || is_admin()) {
            return;
        }

        $inMenu = false;
        foreach ((array) get_field('avabile_dynamic_post_types', 'options') as $type) {
            if ($type['slug'] !== $query->post_type) {
                continue;
            }

            if (!$type['show_posts_in_sidebar_menu']) {
                return;
            }
        }

        $query->set('post_parent', 0);
    }

    /**
     * Makes it possible to have "page" children below a parent page that's a page_for_{post_type}
     * @param  WP_Query $query
     * @return void
     */
    public function enablePageForPostTypeChildren($query)
    {
        if (!$query->is_main_query() || is_admin()) {
            return;
        }

        // Check if page_for_{post_type} isset,  return if not
        $postType = $query->get('post_type');
        if (is_array($postType)) {
            $postType = end($postType);
        }

        $pageForPostType = get_option('page_for_' . $postType);
        if (!$pageForPostType) {
            return;
        }

        // Test if wp_query gives results, return if it does
        $testQuery = new \WP_Query($query->query);
        if ($testQuery->have_posts()) {
            return;
        }

        // Modify query to check for page instead of post_type
        $query->set('post_type', 'page');
        $query->set('child_of', $pageForPostType);
    }

    /**
     * Share a post by email
     * @return void
     */
    public function socialShareEmail()
    {
        if (!is_user_logged_in() && defined('G_RECAPTCHA_KEY')) {
            $response = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';
            $reCaptcha = \Municipio\Helper\ReCaptcha::controlReCaptcha($response);

            if (!$reCaptcha) {
                wp_send_json_error(__('Something went wrong, please try again', 'municipio'));
            }
        }

        $user        = wp_get_current_user();
        $postId      = $_POST['post_id'];
        $senderName  = is_user_logged_in() ? $user->display_name : $_POST['sender_name'];
        $senderEmail = is_user_logged_in() ? $user->user_email : $_POST['sender_email'];
        $recipient   = $_POST['recipient_email'];
        $message     = '<a href="' . get_permalink($postId) . '" target="_blank">' . get_permalink($postId) . '</a>';
        $message    .= !empty($_POST['message']) ? '<br><br>' . $_POST['message'] : '';
        $message    .= '<br><br>---<br>' . sprintf(__('This message was sent by %s via %s', 'municipio'), $senderEmail, '<a href="' . get_site_url() . '" target="_blank">' . get_site_url() . '</a>');

        // Send the email
        $mail = wp_mail(
            $recipient,
            $senderName . ' ' . __('sent you a link via', 'municipio') . ': ' . get_site_url(),
            $message,
            array(
                'From: ' . $senderName . ' <' . $senderEmail . '>',
                'Content-Type: text/html; charset=UTF-8'
            )
        );

        wp_send_json_success(__('The message was sent successfully', 'municipio'));
    }
}
