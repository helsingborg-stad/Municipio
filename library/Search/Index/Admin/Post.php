<?php

namespace Municipio\Search\Index\Admin;

use \Municipio\Search\Index\Helper\Indexable as Indexable;

class Post
{
    private $algolia_index_options;

    public function __construct()
    {
        //Add excludeFromSearchCheckbox
        add_action('post_submitbox_misc_actions', array($this, 'excludeFromSearchCheckbox'), 100);
        add_action('attachment_submitbox_misc_actions', array($this, 'excludeFromSearchCheckbox'), 100);

        //Save actions
        add_action('save_post', array($this, 'saveExcludeFromSearch'), 10);
    }

    /**
     * Print exclude for search checkbox
     *
     * @return void
     */
    public function excludeFromSearchCheckbox()
    {
        global $post;

        //Only show if not set to not index
        if (!in_array($post->post_type, Indexable::postTypes())) {
            return false;
        }

        if (is_object($post) && isset($post->ID)) {
            $checked = checked(true, get_post_meta($post->ID, 'exclude_from_search', true), false);
            echo
                '
          <style scoped="scoped">
            .misc-pub-index:before {
              content: "\f179";
            }
            .misc-pub-index:before {
              font: normal 20px/1 dashicons;
              speak: none;
              display: inline-block;
              margin-left: -1px;
              padding-right: 3px;
              vertical-align: top;
              -webkit-font-smoothing: antialiased;
              -moz-osx-font-smoothing: grayscale;
              color: #828791;
            }
          </style>

          <div class="misc-pub-section misc-pub-index">
            <label>
              <input type="hidden" value="false" name="exclude-from-search">
              <input class="exclude-search-check" type="checkbox" name="exclude-from-search" value="true" '
                . $checked
                . '> 
              '
                . __('Exclude from search', 'municipio')
                    . '
            </label>
          </div>
        '
            ;
        }
    }

    /**
     * Exclude from search toggle option
     *
     * @param int $postId
     * @return bool
     */
    public function saveExcludeFromSearch($postId)
    {
        if (isset($_POST['exclude-from-search']) && $_POST['exclude-from-search'] === 'false') {
            delete_post_meta($postId, 'exclude_from_search');
            return true;
        } elseif (isset($_POST['exclude-from-search']) && $_POST['exclude-from-search'] === 'true') {
            update_post_meta($postId, 'exclude_from_search', true);
            return false;
        }
    }
}
