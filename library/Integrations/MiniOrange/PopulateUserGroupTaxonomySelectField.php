<?php

namespace Municipio\Integrations\MiniOrange;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class PopulateUserGroupTaxonomySelectField implements Hookable
{
    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
    {
    }

    /**
     * Add hooks to load the user group field choices dynamically.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('acf/load_field/name=user_group', array($this, 'populateUserGroupChoices'));
    }

    /**
     * Populate the choices for the User Group field with terms from the user_group taxonomy.
     *
     * @param  array  $field The ACF field
     * @return array  The modified ACF field
     */
    public function populateUserGroupChoices($field): array
    {
        $terms = get_terms(array(
            'taxonomy'   => $this->config->getUserGroupTaxonomy(),
            'hide_empty' => false
        ));

        $choices = [];
        foreach ($terms as $term) {
            $choices[$term->term_id] = $term->name;
        }
        $field['choices'] = $choices;
        return $field;
    }
}
