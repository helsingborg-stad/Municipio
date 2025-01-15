<?php

namespace Municipio\UserGroup;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Populate User Group URL blog id field.
 */
class PopulateUserGroupUrlBlogIdField implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('acf/load_field/key=field_677e6b8534123', array($this, 'populateUserGroupUrlBlogIdField'));
    }

  /**
   * Populate the user group url blog id field
   *
   * @param array $field
   */
    public function populateUserGroupUrlBlogIdField($field)
    {
        if (!$this->wpService->isMultisite()) {
            return $field;
        }
        $field['choices'] = [];
        $blogs            = $this->wpService->getSites(['number' => 500]) ?? [];
        foreach ($blogs as $blog) {
            $field['choices'][$blog->blog_id] = 'https://' . $blog->domain . $blog->path;
        }
        return $field;
    }
}
