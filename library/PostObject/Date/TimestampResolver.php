<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetPostMeta;
use Municipio\PostObject\Date\ArchiveDateSourceResolverInterface;

/**
 * TimestampResolver class.
 */
class TimestampResolver implements TimestampResolverInterface
{
    private $defaultTimestamps = [
        'post_date',
        'post_modified'
    ];

    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private GetPostMeta $wpService,
        private ArchiveDateSourceResolverInterface $archiveDateSetting
    ) {
    }

    /**
     * Resolve the timestamp.
     *
     * @return int
     */
    public function resolve(): ?int
    {
        $archiveDateSetting = $this->archiveDateSetting->resolve();

        if (in_array($archiveDateSetting, $this->defaultTimestamps)) {
            return $this->getDefaultTimestamp($archiveDateSetting);
        }

        if ($archiveDateSetting === 'none') {
            return null;
        }

        return $this->getDateMetaValue($archiveDateSetting);
    }

    /**
     * Get the date meta value.
     */
    private function getDateMetaValue(string $archiveDateSetting): ?int
    {
        $metaValue = $this->wpService->getPostMeta($this->postObject->getId(), $archiveDateSetting, true);

        if ($metaValue) {
            return \Municipio\Helper\StringToTime::convert($metaValue);
        }

        return null;
    }

    /**
     * Get the archive date setting.
     */
    private function getDefaultTimestamp(string $archiveDateSetting): int
    {
        return $archiveDateSetting === 'post_modified' ?
            $this->postObject->getModifiedTime() :
            $this->postObject->getPublishedTime();
    }
}
