<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostObject\PostObjectInterface;

/*
 * View utility to get date timestamp
 */
class GetDateTimestamp implements ViewCallableProviderInterface
{
    /**
     * Constructor
     *
     * @param string $dateSource
     * @param PostObjectInterface[] $posts
     * @param \wpdb $wpdb
     */
    public function __construct(
        private string $dateSource,
        private array $posts,
        private \wpdb $wpdb,
    ) {}

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post) => $this->getTimestamp($post);
    }

    private function getTimestamp(PostObjectInterface $post): int
    {
        if ($this->dateSource === 'post_date') {
            return $post->getPublishedTime();
        }

        if ($this->dateSource === 'post_modified') {
            return $post->getModifiedTime();
        }

        return $this->getTimestampFromMeta($post);
    }

    private function getTimestampFromMeta(PostObjectInterface $post): int
    {
        // For performance reasons, get all posts meta in one call and cache it locally by post ID
        static $allMeta = [];

        if (array_key_exists($post->getId(), $allMeta)) {
            return $allMeta[$post->getId()];
        }

        $query = $this->wpdb->prepare(
            "SELECT post_id, meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND post_id IN ("
            . implode(',', array_map('intval', array_map(fn($p) => $p->getId(), $this->posts)))
            . ')',
            $this->dateSource,
        );
        $results = $this->wpdb->get_results($query);

        foreach ($results as $row) {
            $allMeta[(int) $row->post_id] = is_numeric($row->meta_value)
                ? (int) $row->meta_value
                : strtotime($row->meta_value);
        }

        return $allMeta[$post->getId()] ?? 0;
    }
}
