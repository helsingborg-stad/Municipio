<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class SetGroupAsTaxonomy implements Hookable
{
    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
    {
    }

  /**
   * Add hooks to map the attributes from the idefied provider
   *
   * @return void
   */
    public function addHooks(): void
    {
        $this->wpService->addAction('mo_saml_user_group_name', array($this, 'setGroupAsTaxonomy'), 10, 2);
    }

  /**
   * Set the group as a taxonomy
   *
   * @param string $groupName
   * @param string $groupValue
   * @return void
   */
    public function setGroupAsTaxonomy(int $userId, string $groupName): void
    {
        $taxonomy = $this->config->getUserGroupTaxonomy();
        if ($termId = $this->createOrGetTermFromString($groupName, $taxonomy) && $userId) {
            wp_set_object_terms($userId, $termId, $taxonomy, false);
        }
    }

  /**
   * Create a taxonomy from a string
   *
   * @param string $groupName
   * @param string $taxonomy
   * @return int|null
   */
    private function createOrGetTermFromString(string $groupName, $taxonomy): ?int
    {
        if (empty($groupName)) {
            return null;
        }
        $term = get_term_by('name', $groupName, $taxonomy);
        if (!$term) {
            $result = wp_insert_term($groupName, $taxonomy);
            if (is_wp_error($result)) {
                return null;
            }
            $termId = $result['term_id'];
        } else {
            $termId = $term->term_id;
        }
        return $termId ?? null;
    }
}
