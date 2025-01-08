<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class PopulateUserGroupUrlBlogIdField implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

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
        $field['choices'] = [];
        $blogs            = get_sites(['number' => 500]) ?? [];
        foreach ($blogs as $blog) {
            $field['choices'][$blog->blog_id] = 'https://' . $blog->domain . $blog->path;
        }
        return $field;
    }
}
