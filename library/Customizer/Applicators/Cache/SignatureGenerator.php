<?php

namespace Municipio\Customizer\Applicators\Cache;

use wpdb;
use WpService\WpService;
use Kirki\Compatibility\Kirki as KirkiCompatibility;

/**
 * Generates signatures for cache invalidation based on content changes
 */
class SignatureGenerator implements SignatureGeneratorInterface
{
    public function __construct(
        private WpService $wpService,
        private wpdb $wpdb
    ) {
    }

    /**
     * Generate a signature for the given data
     *
     * @param array $data The data to generate signature for
     * @return string The generated signature
     */
    public function generateSignature(array $data): string
    {
        $supportedHashes = hash_algos() ?? [];
        if (in_array('xxh3', $supportedHashes)) {
            $hash = hash('xxh3', json_encode($data));
        } else {
            $hash = hash('md5', json_encode($data));
        }

        return $this->shortenHash($hash);
    }

    /**
     * Get the customizer fields signature
     *
     * @return string The fields signature
     */
    public function getCustomizerFieldSignature(): string
    {
        $fields = [];
        if (class_exists('\Kirki\Compatibility\Kirki')) {
            $fields = array_merge(
                KirkiCompatibility::$fields ?? [],
                KirkiCompatibility::$all_fields ?? [],
                $fields
            );
        }
        return $this->generateSignature($fields);
    }

    /**
     * Get the last published timestamp
     *
     * @return string The timestamp or 'unknown'
     */
    public function getLastPublishedTimestamp(): string
    {
        $postStatus = $this->getCustomizerStateKey() === 'draft' ?
            ['draft', 'auto-draft', 'inherit', 'future', 'trash', 'publish'] :
            ['publish'];

        $latestDate = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT post_modified_gmt 
                FROM {$this->wpdb->posts} 
                WHERE post_type = %s 
                AND post_status IN ('" . implode("','", array_map('esc_sql', $postStatus)) . "')
                ORDER BY post_modified_gmt DESC 
                LIMIT 1",
                'customize_changeset'
            )
        );

        return $latestDate ? strtotime($latestDate) : 'unknown';
    }

    /**
     * Get the customizer state key
     *
     * @return string
     */
    private function getCustomizerStateKey(): string
    {
        return $this->wpService->isCustomizePreview() ? 'draft' : 'publish';
    }

    /**
     * Shorten hash to 8 characters
     *
     * @param string $hash The hash to shorten
     * @return string
     */
    private function shortenHash(string $hash): string
    {
        return substr($hash, 0, 8);
    }
}