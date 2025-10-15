<?php

namespace Modularity\Module\Curator;

use Modularity\Helper\Block;

class Curator extends \Modularity\Module
{
    public $slug = 'curator';
    public $supports = array();
    public $cacheTtl = (60 * 12); //Minutes (12 hours)

    public function init()
    {
        $this->nameSingular = __('Curator Social Media', 'modularity');
        $this->namePlural = __('Curator Social Media', 'modularity');
        $this->description = __("Output social media flow via curator.", 'modularity');

        $this->data['i18n'] = [
        'loadMore' => __('Load More', 'modularity'),
        'goToOriginalPost' => __('Go to original post', 'modularity'),
        'noMoreItems' => __('No more items to load.', 'modularity'),
        ];

        add_action('wp_ajax_mod_curator_get_feed', [$this, 'getFeed'], 10, 4);
        add_action('wp_ajax_nopriv_mod_curator_get_feed', [$this, 'getFeed'], 10, 4);

        add_action('wp_ajax_mod_curator_load_more', [$this, 'loadMorePosts']);
        add_action('wp_ajax_nopriv_mod_curator_load_more', [$this, 'loadMorePosts']);
    }

    public function loadMorePosts()
    {
        if (!$this->isAjaxRequest()) {
            return false;
        }
        if (empty($_POST['posts'])) {
            return false;
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mod-posts-load-more')) {
            wp_die('Nonce check failed');
            return false;
        }

        $posts = json_decode(stripslashes($_POST['posts']));
        if (!is_array($posts)) {
            wp_die();
        }

        $posts = self::parseSocialMediaPosts($posts);
        $i18n = $this->data['i18n'];

        $layout = empty($_POST['layout']) ? 'card' : $_POST['layout'];

        // Print the posts via the blade template
        echo render_blade_view(
            "partials/$layout",
            [
            'posts' => $posts,
            'i18n' => $i18n,
            'columnClasses' => $_POST['columnClasses']
            ],
            [
            plugin_dir_path(__FILE__) . 'views'
            ]
        );
        wp_die(); // Always die in functions echoing ajax content
    }

    public function script()
    {
        wp_register_script(
            'mod-curator-load-more',
            MODULARITY_URL . '/dist/' . \Modularity\Helper\CacheBust::name('js/mod-curator-load-more.js')
        );
        $strings = array_merge($this->data['i18n'], [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mod-posts-load-more')
        ]);
        wp_localize_script('mod-curator-load-more', 'curator', $strings);
        wp_enqueue_script('mod-curator-load-more');
    }

    public function data(): array
    {
        //Default values
        $data = [
            'i18n'          => $this->data['i18n'],
            'columnClasses' => '',
            'ratio'         => '',
            'gutter'        => '',
            'showFeed'      => false,
            'posts'         => [],
            'postCount'     => 0
        ];

        //Get module fields
        $fields = $this->getFields();

        //General fields 
        $data['embedCode']      = $this->parseEmbedCode($fields['embed_code']);
        $data['numberOfItems']  = $fields['number_of_posts'] ?: 12;
        $data['layout']         = $fields['layout'] ?: 'card';
        $data['columns']        = $fields['columns'] ?: 4;
        $data['showPoweredBy']  = isset($fields['show_powered_by']) ? (bool) $fields['show_powered_by'] : false;

        //Exclusive fields for blocks
        if ($data['layout'] === 'block') {
            $data['ratio']  = $fields['ratio'] ?: '4:3';
            $data['gutter'] = $fields['gutter'] ? 'o-grid--no-gutter' : '';
        }

        //Calc column size
        $data['columnClasses'] .= $this->getGridClass($data['columns']);

        //Fetch feed data
        $feed = $this->getFeed(
            $data['embedCode'], 
            (int) $data['numberOfItems'] + 1,
            0, 
            (bool) !isset($_GET['flush'])
        );

        //Assign to params
        if (!empty($feed->posts)) {
            $data['showFeed']  = true;
            $data['posts']     = $feed->posts;
            $data['postCount'] = $feed->postCount;
        }

        //Parse posts array
        $data['posts'] = is_array($data['posts']) ? self::parseSocialMediaPosts($data['posts']) : [];

        //Could not fetch error message / embed code error message
        if (!$data['embedCode']) {
            $data['errorMessage'] = __("An invalid embed code was provided.", 'modularity');
        } else {
            $data['errorMessage'] = __("Could not get the feed at this moment, please try again later.", 'modularity');
        }

        //Send to view
        return $data;
    }

    /**
     * Returns the appropriate grid class based on the number of columns.
     *
     * @param int $columns The number of columns.
     * @return string The grid class.
     */
    private function getGridClass($columns) {
        if($columns == 3) {
            return 'o-grid-4@md o-grid-6@sm';
        }
        return 'o-grid-3@xl o-grid-3@lg o-grid-4@md o-grid-6@sm'; 
    } 

    /**
     * Parses the social media posts data to add additional properties and modify existing ones.
     *
     * @param array $posts The social media posts data to parse.
     *
     * @return array The parsed social media posts data.
     */
    public static function parseSocialMediaPosts(array $posts = []): array
    {
        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $key => $post) {
                if (self::isCuratorUser($post)) {
                    unset($posts[$key]);
                    continue;
                }

                $post->full_text            = $post->text ?? '';
                $post->user_readable_name   = self::getUserName($post->user_screen_name);
                $post->text                 = wp_trim_words($post->text, 20, "...") ?? '';

                // Prepare oembed
                if (in_array($post->network_name, ['YouTube', 'Vimeo'], true)) {
                    global $wp_embed;
                    $post->oembed = $wp_embed->shortcode([], $post->url);
                }

                // Format date
                $post->formatted_date = date_i18n('j M. Y', strtotime($post->source_created_at));

                // Set title
                if (!empty($post->data) && empty($post->title)) {
                    foreach ($post->data as $item) {
                        if ($item->name == 'title') {
                            $post->title = $item->value;
                        }
                    }
                }

                $posts[$key] = $post;
            }
        }

        return $posts;
    }

    /**
     * Check if the post author is a Curator.io user.
     *
     * @param WP_Post $post The post object to check.
     * @return bool True if the post author is a Curator.io user, false otherwise.
     */
    private static function isCuratorUser($post)
    {
        if ('curator_io' === $post->user_screen_name || 'https://curator.io' === $post->url) {
            return true;
        }
        return false;
    }

    /**
     * Parse embed javascript to get the embed code
     *
     * @param   string $embed   Embed javascript string
     *
     * @return  string $embed   Embed code
     */
    private function parseEmbedCode($embed)
    {

        if (preg_match('/published\/(.*?)\.js/i', $embed, $match) == 1) {
            return $match[1];
        }

        return false;
    }

    /**
     * Get username as readable from user string
     *
     * @param   string $userName
     * @return  string $userName
     */
    public static function getUserName(string $userName = ''): string
    {
        return ucwords(str_replace(['.', '-'], ' ', $userName));
    }
    /**
     * Retrieves social media feed data for the given embed code from Curator.io.
     *
     * @see https://curator.io/docs/api
     *
     * @param string $embedCode The embed code for the social media feed.
     * @param int $numberOfItems The number of social media posts to retrieve.
     * @param int $offset The offset of the first post to retrieve.
     * @param bool $cache Whether to use the WordPress transient cache for caching the feed response.
     *
     * @return object The social media feed data as an object.
     */
    public function getFeed(string $embedCode = '', int $numberOfItems = 13, int $offset = 0, bool $cache = true)
    {
        if ($this->isAjaxRequest()) {
            if (!empty($_POST['embed-code'])) {
                $embedCode     = $_POST['embed-code'];
                $numberOfItems = (int) $_POST['limit'] + 1;
                $offset        = $_POST['offset'];
            } else {
                wp_die('embed code not found');
            }
        }

        $requestUrl = "https://api.curator.io/restricted/feeds/{$embedCode}/posts";

        $requestArgs = [
            'headers' => [
                'Content-Type: application/json',
            ],
            'body' => [
                'limit'        => $numberOfItems,
                'offset'       => $offset,
                'hasPoweredBy' => 1,
                'version'      => '4.0',
                'status'       => 1
            ]
        ];

        $feed = $this->maybeRetriveCachedResponse($requestUrl, $requestArgs, $cache);

        if ($this->isAjaxRequest()) {
            wp_die($feed);
        }

        return json_decode($feed);
    }

    /**
     * Retrieve cached response if available or get remote response and set cached response.
     *
     * @param string $requestUrl The URL to request.
     * @param array $requestArgs Optional. Arguments for the remote request.
     * @param bool $cache Whether to use cached response or not.
     * @return mixed Cached response if available or remote response.
     */
    private function maybeRetriveCachedResponse($requestUrl, $requestArgs, $cache)
    {

        $transientKey = $this->createTransientKey($requestUrl, $requestArgs);

        if ($cache && $cachedFeed = get_transient($transientKey)) {
            return $cachedFeed;
        }

        return $this->getRemoteAndSetCachedResponse($requestUrl, $requestArgs, $transientKey);
    }

    /**
     * Get remote response and set cached response.
     *
     * @param string $requestUrl The URL to request.
     * @param array $requestArgs Optional. Arguments for the remote request.
     * @param string $transientKey The transient key for caching the response.
     * @return mixed Remote response.
     */
    private function getRemoteAndSetCachedResponse($requestUrl, $requestArgs, $transientKey)
    {
        $feed = wp_remote_retrieve_body(wp_remote_get($requestUrl, $requestArgs));

        if ($feed) {
            set_transient($transientKey, $feed, MINUTE_IN_SECONDS * $this->cacheTtl);
        }

        return $feed;
    }

    /**
     * Create a transient key for caching the response.
     *
     * @param string $requestUrl The URL to request.
     * @param array $requestArgs Optional. Arguments for the remote request.
     * @return string The transient key for caching the response.
     */
    private function createTransientKey($requestUrl, $requestArgs)
    {
        return "curator_" . md5(serialize($requestUrl) . serialize($requestArgs));
    }

    /**
     * Check if the request is an AJAX request.
     *
     * @return bool True if the request is an AJAX request, false otherwise.
     */
    private function isAjaxRequest()
    {
        return (bool) (defined('DOING_AJAX') && DOING_AJAX);
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
