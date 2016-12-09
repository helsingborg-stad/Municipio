<?php

namespace Intranet\User;

class Profile
{
    protected $urlBase = 'user';

    public function __construct()
    {
        $this->urlBase = __('user', 'municipio-intranet');

        // View profile
        add_action('init', array($this, 'profileUrlRewrite'));
        add_action('template_redirect', array($this, 'accessControl'), 5);
        add_action('template_redirect', array($this, 'inactiveControl'), 5);
        add_filter('wp_title', array($this, 'setProfileTitle'), 11, 3);

        // Edit profile
        add_action('init', array($this, 'editProfileUrlRewrite'));
        add_filter('template_include', array($this, 'editProfileTemplate'), 10);

        add_filter('Municipio/controller/base/view_data', array($this, 'currentUserData'));

        add_filter('Modularity/adminbar/editor_link', array($this, 'profileEditModularityEditorLink'), 10, 4);
    }

    public function profileEditModularityEditorLink($editorLink, $post, $archiveSlug, $currentUrl)
    {
        if ($archiveSlug == 'author' && empty($editorLink) && strpos($currentUrl, '/edit')) {
            $editorLink = admin_url('options.php?page=modularity-editor&id=author-edit');
        }

        return $editorLink;
    }

    public function currentUserData($data)
    {
        $data['currentUser'] = wp_get_current_user();
        return $data;
    }

    /**
     * Use correct template for the profile edit page
     * @param  string $template Default template
     * @return string           Template to use
     */
    public function editProfileTemplate($template)
    {
        global $wp_query;

        if ($wp_query->is_404()) {
            return get_404_template();
        }

        // Bail if not on edit page
        if (!isset($wp_query->query['author_name']) || empty($wp_query->query['author_name']) || !isset($wp_query->query['editprofile']) || !$wp_query->query['editprofile'] || $wp_query->query['editprofile'] == 'false') {
            return $template;
        }

        if (!get_user_by('slug', $wp_query->query['author_name'])) {
            $wp_query->set404();
            return get_404_template();
        }

        $template = \Municipio\Helper\Template::locateTemplate('author-edit');
        return $template;
    }

    /**
     * Only show author/profile page to logged in users
     * @return void
     */
    public function accessControl()
    {
        global $wp_query;
        $currentUser = wp_get_current_user();

        if (is_author() && !is_user_logged_in()) {
            $wp_query->set_404();
        }

        if (isset($wp_query->query['editprofile']) && $wp_query->query['editprofile'] && (!is_super_admin() && $currentUser->user_login != $wp_query->query['author_name'])) {
            $wp_query->set_404();
        }
    }

    /**
     * Only show author/profile if is enabled user
     * @return void
     */
    public function inactiveControl()
    {
        if (!is_author()) {
            return;
        }

        global $wp_query;

        $user = get_user_by('login', $wp_query->query['author_name']);

        if (isset($user->data->user_email)) {

            //Remove disabled users
            if (0 === strpos($user->data->user_email, 'DISABLED-USER-')) {
                $wp_query->set_404();
            }

            //Remove users not having an email
            if (empty($user->data->user_email)) {
                $wp_query->set_404();
            }

            //Remove users that are considerd an s-account
            if (preg_match('/^s([a-z]{4})([0-9]{4})/i', $user->data->user_login)) {
                $wp_query->set_404();
            }
        }
    }

    /**
     * Change the url base for the author pages to /user
     * @return void
     */
    public function profileUrlRewrite()
    {
        global $wp_rewrite;

        if ($wp_rewrite->author_base != $this->urlBase) {
            $wp_rewrite->author_base = $this->urlBase;
            $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';

            flush_rewrite_rules();
        }
    }

    /**
     * Set the page title for author page
     * @param string $title       The original title
     * @param string $sep         The separator character
     * @param string $seplocation The separator location
     * @return  string [<description>]
     */
    public function setProfileTitle($title, $sep, $seplocation)
    {
        if (is_author()) {
            $title = municipio_intranet_get_user_full_name(get_the_author_meta('ID'));

            switch ($seplocation) {
                case 'right':
                    $title .= ' ' . $sep . ' ';
                    break;

                case 'left':
                    $title = ' ' . $sep . ' ' . $title;
            }
        }

        return $title;
    }

    /**
     * Adds edit page
     * @return void
     */
    public function editProfileUrlRewrite()
    {
        add_rewrite_rule('^' . $this->urlBase .'\/([a-zA-Z0-9_-]+)\/edit', 'index.php?author_name=$matches[1]&editprofile=true&modularity_template=author-edit', 'top');
        add_rewrite_tag('%editprofile%', 'true');
        add_rewrite_tag('%modularity_template%', '(*.)');

        flush_rewrite_rules();
    }
}
