<?php

declare(strict_types=1);


namespace Municipio\Search\Index\Admin;

use \Municipio\Search\Index\Helper\Indexable as Indexable;
use WpService\Contracts\AddAction;
use WpService\Contracts\Checked;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\UpdatePostMeta;

class Post
{
    public function __construct(private AddAction&Checked&GetPostMeta&DeletePostMeta&UpdatePostMeta $wpService)
    {
        //Add excludeFromSearchCheckbox
        $this->wpService->addAction('post_submitbox_misc_actions', [$this, 'excludeFromSearchCheckbox'], 100);
        $this->wpService->addAction('attachment_submitbox_misc_actions', [$this, 'excludeFromSearchCheckbox'], 100);

        //Save actions
        $this->wpService->addAction('save_post', [$this, 'saveExcludeFromSearch'], 10);
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
        if (!in_array($post->post_type, Indexable::postTypes(), strict: true)) {
            return false;
        }

        if (is_object($post) && isset($post->ID)) {
            $checked = $this->wpService->checked(true, $this->wpService->getPostMeta($post->ID, 'exclude_from_search', true), false);
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
            $this->wpService->deletePostMeta($postId, 'exclude_from_search');
            return true;
        } elseif (isset($_POST['exclude-from-search']) && $_POST['exclude-from-search'] === 'true') {
            $this->wpService->updatePostMeta($postId, 'exclude_from_search', true);
            return false;
        }
    }
}
