<?php

namespace Modularity\Helper;


/**
 * Class UpdateDateOnPostsRelatedToModule
 *
 * This class is responsible for updating the modified date of posts related to a module.
 */
class UpdateDateOnPostsRelatedToModule {

    /**
     * UpdateDateOnPostsRelatedToModule constructor.
     *
     * @param \Modularity\ModuleManager $moduleManager The module manager instance.
     */
    public function __construct(private \Modularity\ModuleManager $moduleManager) {
    }

    /**
     * Update the modified date of posts related to a module.
     *
     * @param int $modulePostId The ID of the module post.
     * @return void
     */
    public function update(\WP_Post $modulePost):void {

        if(\str_starts_with($modulePost->post_type, 'mod-') === false) {
            return;
        }

        $postsUsingModule = $this->moduleManager->getModuleUsage($modulePost->ID);

        // Bail early if no posts are using this module
        if (empty($postsUsingModule)) {
            return;
        }

        // Update post_modified date on all posts using the module
        foreach ($postsUsingModule as $post) {
            wp_update_post([
                'ID' => $post->post_id,
                'post_modified' => $modulePost->post_modified,
                'post_modified_gmt' => get_gmt_from_date($modulePost->post_modified)
            ]);
        }
    }
}
