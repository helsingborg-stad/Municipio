<?php

namespace Municipio\Upgrade\V39;

use Municipio\Customizer\Applicators\Types\NullApplicator;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\DoAction;
use WpService\Contracts\AddAction;

/**
 * Class Version39
 */
class Version39 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private \wpdb $wpdb,
        private GetThemeMod&SetThemeMod&GetPostTypes&AddAction&DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $countedValues = array_reduce(
            [
            'mod_contacts_list_modifier',
            'mod_contacts_card_modifier',
            'mod_index_modifier',
            'mod_inlay_list_modifier',
            'mod_localevent_modifier',
            'mod_map_modifier',
            'mod_posts_expandablelist_modifier',
            'mod_posts_list_modifier',
            'mod_posts_index_modifier',
            'mod_script_modifier',
            'mod_section_split_modifier',
            'mod_text_modifier',
            'mod_video_modifier'
            ],
            function ($carry, $key) {
                $value = $this->wpService->getThemeMod($key) ?: 'none';
                $value = !is_string($value) ? 'none' : $value;

                if (!isset($carry[$value])) {
                    $carry[$value] = 0;
                }

                $carry[$value]++;
                return $carry;
            },
            []
        );

        $maxKey = array_search(max($countedValues), $countedValues);


        if ($maxKey === false) {
            $maxKey = 'none';
        }

        $this->wpService->setThemeMod('component_card_modifier', $maxKey);

        $applicators     = [
            new NullApplicator(),
        ];
        $customizerCache = new \Municipio\Customizer\Applicators\ApplicatorCache(
            $this->wpService,
            $this->wpdb,
            ...$applicators
        );
        $customizerCache->tryClearCache();
    }
}
