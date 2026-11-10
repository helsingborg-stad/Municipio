<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Admin;

use Municipio\EasyRead\Config\EasyReadConfigInterface;
use WpService\Contracts\AddFilter;
use WpService\Contracts\__;

/** Registers the legacy ACF location rule used by the Easy Read field group. */
final class AcfLocationRules
{
    public function __construct(
        private AddFilter&__ $wpService,
        private EasyReadConfigInterface $config,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('acf/location/rule_types', [$this, 'addRuleType']);
        $this->wpService->addFilter('acf/location/rule_values/settings', [$this, 'addRuleValues']);
        $this->wpService->addFilter('acf/location/rule_match/settings', [$this, 'matches'], 10, 3);
    }

    public function addRuleType(array $choices): array
    {
        $choices['Easy reading']['settings'] = $this->wpService->__('Post types', 'municipio');
        return $choices;
    }

    public function addRuleValues(array $choices): array
    {
        $choices['post_types'] = $this->wpService->__('Selected', 'municipio');
        return $choices;
    }

    /** @mago-expect lint:no-boolean-flag-parameter The ACF hook determines the initial match state. */
    public function matches(bool $match, array $rule, array $screen): bool
    {
        $postType = $screen['post_type'] ?? null;
        $postId = (int) ($screen['post_id'] ?? 0);

        if (!is_string($postType) || $postId <= 0) {
            return $match;
        }

        $isSelected = in_array($postType, $this->config->enabledPostTypes(), true);
        return ($rule['operator'] ?? '==') === '!=' ? !$isSelected : $isSelected;
    }
}
