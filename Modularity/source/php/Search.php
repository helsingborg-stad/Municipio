<?php

namespace Modularity;

class Search
{
    public function __construct()
    {

        add_action('wp', array($this, 'moduleSearch'));

        add_filter('posts_join', array($this, 'moduleSearchModuleDescriptionJoin'));
        add_filter('posts_search', array($this, 'moduleSearchModuleDescription'));

        //Elasticpress
        add_filter('ep_pre_index_post', array($this, 'elasticPressPreIndex'));
        add_filter('ep_post_sync_args_post_prepare_meta', array($this, 'elasticPressPreIndex'));

        //Algolia
        add_filter('algolia_post_shared_attributes', array($this, 'addAlgoliaModuleAttribute'), 10, 2);
        add_filter('algolia_searchable_post_shared_attributes', array($this, 'addAlgoliaModuleAttribute'), 10, 2);
        add_filter('algolia_should_index_searchable_post', array($this, 'shouldIndexPost'), 50, 2);
    }

    /**
     * This method will switch module search results with posts the module is used in
     * @return void
     */
    public function moduleSearch()
    {
        global $wp_query;

        //Only run on search
        if (!$wp_query->is_search()
            || is_admin()
            || $wp_query->query_vars['post_type'] !== 'any') {
            return;
        }

        //Store result
        $searchResult = $wp_query->posts;

        //Check for mod & get relations
        foreach ($wp_query->posts as $key => $post) {
            // Continue if not a modularity post type
            if (substr($post->post_type, 0, 4) != 'mod-') {
                continue;
            }

            // Find module usage
            $usage = \Modularity\ModuleManager::getModuleUsage($post->ID);

            $usagePosts = array();
            foreach ($usage as $item) {
                $usagePosts[] = get_post($item->post_id);
            }

            $searchResult = $this->appendToArray($searchResult, $key, $usagePosts);
            unset($searchResult[$key]);
        }

        //Remove modularity duplicated
        $searchResult = array_filter($searchResult, function ($object) {
            if (substr($object->post_type, 0, 4) != 'mod-') {
                return true;
            }
            return false;
        });

        //Number of posts
        $foundPosts = count($searchResult);

        //"Return"
        $wp_query->posts = array_values($searchResult);
        $wp_query->found_posts = $foundPosts;
        $wp_query->post_count = $foundPosts;

        $postsPerPage = (int) get_option('posts_per_page');
        if ($foundPosts != 0 && $postsPerPage != 0) {
            $wp_query->max_num_pages = $foundPosts / $postsPerPage;
        } else {
            $wp_query->max_num_pages = 0;
        }
    }

    /**
     * Render all modules on post
     * @param  WP_Post $post
     * @return string
     */

    public function getRenderedPostModules($post)
    {
        if (!$post) {
            return;
        }

        if (is_array($post)) {
            $postId = $post['ID'];
        } else {
            $postId = $post->ID;
        }

        $modules = \Modularity\Editor::getPostModules($postId);
        $onlyModules = array();

        // Normalize modules array
        foreach ($modules as $sidebar => $item) {
            if (!isset($item['modules']) || count($item['modules']) === 0) {
                continue;
            }

            $onlyModules = array_merge($onlyModules, $item['modules']);
        }

        // Render modules and append to post content
        $rendered = "<br><br>";
        foreach ($onlyModules as $module) {
            if ($module->post_type === 'mod-wpwidget') {
                continue;
            }

            $markup = \Modularity\App::$display->outputModule($module, array('edit_module' => false), array(), false);

            if(!empty($markup)) {
                $rendered .= " " . $markup;


            }

        }

        return $rendered;
    }

    /**
     * Remove post types from index that are hidden for the user
     * @param bool $should_index The decition originally done by algolia
     * @param WpPost $post The post that should be indexed or not
     * @param bool $includeCheckbox If the check should take the users decition in consideration
     * @return bool True if add, false if not indexable
     */

    public function shouldIndexPost($should_index, $post, $includeCheckbox = true)
    {
        //Get post type object
        if (isset($post->post_type)) {

            //Do not index posts that belong to modularity
            if(strpos($post->post_type, "mod-") === 0) {
                return false;
            }
        }

        return $should_index;
    }

    /**
     * Add a attribute to algolia search
     * @param array   $attributes
     * @param WP_Post $post
     * @return array
     */
    public function addAlgoliaModuleAttribute($attributes, $post)
    {
        //Get rendered data from module(s)
        $rendered = trim(
            preg_replace(
                '!\s+!',
                ' ',
                strip_tags($this->getRenderedPostModules($post))
            )
        );

        //Calculate content bytes
        if (is_array($post)) {
            $contentBytes = strlen($post['post_content']);
        } else {
            $contentBytes = strlen($post->post_content);
        }

        //Only add if not empty
        if (!empty($rendered)) {
            $attributes['modules'] = substr(
                $rendered,
                0,
                (9000 - $contentBytes)
            );
        }

        return $attributes;
    }

    /**
     * Add modules to post_content before indexing a post (makes modules searchable)
     * @param  WP_Post $post
     * @return post object
     */
    public function elasticPressPreIndex($post)
    {
        $rendered = $this->getRenderedPostModules($post);

        if (is_array($post)) {
            $post['post_content'] .= $rendered;
        } else {
            $post->post_content .= $rendered;
        }

        return $post;
    }

    /**
     * Adds the OR condition to search the module description on module edit pages.
     * @param string   $search Search SQL for WHERE clause.
     * @return string
     */
    public function moduleSearchModuleDescription($search)
    {
        global $wpdb;

        if ($this->isModuleSearch()) {
            $like = '%' . $wpdb->esc_like($_GET['s']) . '%';
            $meta_description = $wpdb->prepare("OR ({$wpdb->postmeta}.meta_value LIKE %s)", $like);
            // Add the meta description OR condition between one of the existing OR conditions.
            $search = str_replace('OR', $meta_description . ' OR', $search);
        }

        return $search;
    }

    /**
     * Adds a join for the module description.
     * @param string   $join The JOIN clause of the query.
     * @return string
     */
    public function moduleSearchModuleDescriptionJoin($join)
    {
        global $wpdb;

        if ($this->isModuleSearch()) {
            $join .= "LEFT JOIN $wpdb->postmeta ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND meta_key = 'module-description'";
        }

        return $join;
    }

    /**
     * Helper method to determine if a module search is performed on a module edit page.
     * @return bool
     */
    public function isModuleSearch()
    {
        global $pagenow;

        if ($pagenow == 'edit.php' && isset($_GET['s']) && $_GET['s'] !== '') {
            $enabled = \Modularity\ModuleManager::$enabled;
            if (in_array($_GET['post_type'], $enabled)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Appends items to array after specific key
     * @param  array  $array The array to append to
     * @param  string $key   The key to append after
     * @param  array  $new   The items to append
     * @return array         The new array
     */
    public function appendToArray(array $array, $key, array $new)
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        $pos = false === $index ? count($array) : $index + 1;

        return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    }
}
