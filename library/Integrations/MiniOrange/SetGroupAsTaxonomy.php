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
   * @param int $userId
   * @param string|array $groupName
   * @return void
   */
    public function setGroupAsTaxonomy(int $userId, string|array $groupName): void
    {
        $taxonomy  = $this->config->getUserGroupTaxonomy();
        $groupName = $this->getGroupNameFromMixed($groupName);

        if(!$taxonomy) {
            return;
        }

        if(!$groupName || is_numeric($groupName)) {
            return;
        }

        if(!$userId) {
            return;
        }

        if ($termId = $this->createOrGetTermFromString($groupName, $taxonomy)) {
            $this->wpService->wpSetObjectTerms($userId, $termId, $taxonomy, false);
        }
    }

    /**
     * Set the group as a taxonomy
     *
     * @param string|array $groupName
     * @return string|null $groupName
     */
    private function getGroupNameFromMixed(mixed $groupName): ?string
    {
        if (empty($groupName)) {
            return null;
        }
        if (is_array($groupName)) {
            $groupName = $groupName[0] ?? null;
        }
        return $groupName;
    }

  /**
   * Create a taxonomy from a string
   *
   * @param string|array $groupName
   * @param string $taxonomy
   * @return int|null
   */
    private function createOrGetTermFromString(string $groupName, $taxonomy): ?int
    {
        $term = $this->wpService->getTermBy('name', $groupName, $taxonomy);
        if (!$term) {
            $result = $this->wpService->wpInsertTerm($groupName, $taxonomy);
            if ($this->wpService->isWpError($result)) {
                return null;
            }
            $termId = $result['term_id'];
        } else {
            $termId = $term->term_id;
        }
        return $termId ?? null;
    }
}
