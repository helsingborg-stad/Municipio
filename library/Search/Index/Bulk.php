<?php

namespace Municipio\Search\Index;

use \Municipio\Search\Index\Helper\Indexable as Indexable;
use \Municipio\Search\Index\Helper\Options as Options;

class Bulk
{
    private $prefix = 'algolia-index' . ' ';

    public function __construct()
    {
        //Build command
        \WP_CLI::add_command($this->prefix . 'build', array($this, 'build'));
    }

    /**
     * Build index
     *
     * @param array $args
     * @param array $assocArgs
     * @return void
     */
    public function build($args, $assocArgs)
    {
        $provider = isset($assocArgs['provider']) ? $assocArgs['provider'] : null;
        $instance = Provider\ProviderFactory::createFromEnv($provider);
        $databaseIndex = new Index(false, $instance);

        if (!Options::isConfigured()) {
            \WP_CLI::log('Search must be configured before indexing, terminating...');
            return;
        }

        //Send settings
        if (isset($assocArgs['settings']) && $assocArgs['settings'] == 'true') {
            \WP_CLI::log('Sending settings...');
            $instance->setSettings();
        }

        // Clear index if flag is true
        if (isset($assocArgs['clearindex']) && $assocArgs['clearindex'] == 'true') {
            \WP_CLI::log('Clearing index...');
            $instance->clearObjects();
        }

        \WP_CLI::log('Starting index build for site ' . get_option('home'));

        $postTypes = Indexable::postTypes();

        if (is_array($postTypes) && !empty($postTypes)) {
            global $post;
            $globalPost = $post;

            foreach ($postTypes as $postType) {
                $posts = (array) $this->getPosts($postType);
                if (is_array($posts) && !empty($posts)) {
                    foreach ($posts as $postToIndex) {
                        // Set global post object to current post to enable using it in code being indexed.
                        $post = $postToIndex;

                        \WP_CLI::log("Indexing '" . $postToIndex->post_title . "' of posttype " . $postType);
                        $databaseIndex->index($postToIndex);
                    }
                }
            }

            $post = $globalPost;
        } else {
            \WP_CLI::error('Could not find any indexable posttypes. This will occur when no content is public.');
        }

        \WP_CLI::success('Build done!');
    }

    /**
     * Get posts to try to index.
     *
     * @param string $postType
     * @return array
     */
    public function getPosts($postType)
    {
        return get_posts([
            'post_type' => $postType,
            'numberposts' => -1,
            'suppress_filters' => false,
        ]);
    }
}
