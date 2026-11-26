<?php

declare(strict_types=1);

namespace Modularity\Helper;

use Modularity\Helper\FragmentCache\Fragment;
use Modularity\Helper\FragmentCache\FragmentHash;
use Modularity\Helper\FragmentCache\FragmentStoreFactory;
use Modularity\Helper\FragmentCache\FragmentStoreInterface;
use Municipio\Helper\WpService;

/**
 * Fragment cache for rendered module markup.
 *
 * Usage:
 * $cache = new Modularity\Helper\Cache($post->ID, $module);
 * if ($cache->start()) {
 *     // Your cacheable content here
 *     $cache->stop();
 * }
 */
class Cache
{
    private int|string $postId;
    private ?int $ttl;
    private string|false $hash;
    private bool $isCapturing = false;

    /**
     * @param  int|string|null $postId          The post the fragment belongs to. Saving it clears the fragment, see CachePurge.
     * @param  mixed           $module          Any input data altering the output, as a string, array or object.
     * @param  int|null        $ttl             The time that a fragment should live (in seconds).
     * @param  mixed           $cacheGroup      Request data that alters the output, such as query vars.
     * @param  FragmentAssetContextInterface|null $fragmentAssets Captures and restores the assets of a fragment.
     * @param  FragmentStoreInterface|null $fragmentStore Where fragments are stored. Defaults to the object cache of the current site.
     *
     * @mago-expect lint:excessive-parameter-list
     */
    public function __construct(
        $postId,
        $module = '',
        $ttl = 3600 * 24,
        $cacheGroup = null,
        private ?FragmentAssetContextInterface $fragmentAssets = null,
        private ?FragmentStoreInterface $fragmentStore = null,
    ) {
        $this->postId = $postId ?? '';
        $this->ttl = is_numeric($ttl) ? (int) $ttl : null;
        $this->hash = (new FragmentHash())->create($module, $cacheGroup);
    }

    /**
     * Starts the "cache engine"
     * @return boolean Returns true if the content should be rendered, false if a cached fragment was printed
     */
    public function start()
    {
        if (!$this->isActive()) {
            return true;
        }

        $fragment = $this->getFragmentStore()->get($this->postId, $this->hash);

        if ($fragment === null) {
            $this->startCapturing();
            return true;
        }

        $this->replay($fragment);
        return false;
    }

    /**
     * Stops the cache engine, saves the rendered output as a fragment and prints it
     * @return boolean
     */
    public function stop()
    {
        if (!$this->isCapturing) {
            return false;
        }

        $this->isCapturing = false;
        $html = (string) ob_get_clean();
        $assets = $this->fragmentAssets?->endFragment() ?? [];

        do_action('Modularity/Cache/Markup', $html);

        if ($html !== '') {
            $this->getFragmentStore()->set(
                $this->postId,
                $this->hash,
                new Fragment($html . $this->fragmentTag(), $assets),
                (int) $this->ttl,
            );
        }

        echo $html;
        return true;
    }

    private function startCapturing(): void
    {
        $this->fragmentAssets?->beginFragment();
        ob_start();
        $this->isCapturing = true;
    }

    private function replay(Fragment $fragment): void
    {
        do_action('Modularity/Cache/Markup', $fragment->html);

        $this->fragmentAssets?->restoreFragment($fragment->assets);

        echo $fragment->html;
    }

    private function getFragmentStore(): FragmentStoreInterface
    {
        return $this->fragmentStore ??= (new FragmentStoreFactory(WpService::get(), WpService::get()))->create();
    }

    /**
     * Output fragment cache fingerprint in source code
     * @return string
     */
    private function fragmentTag()
    {
        return '<!-- Modularity Fragment Cache: (' . current_time('Y-m-d H:i:s', 1) . '|' . $this->hash . ') -->';
    }

    /**
     * Check if cache engine shoud be used
     * @return boolean
     */
    private function isActive()
    {
        if (defined('MODULARITY_DISABLE_FRAGMENT_CACHE') && MODULARITY_DISABLE_FRAGMENT_CACHE === true) {
            return false;
        }

        return $this->ttl !== null && $this->ttl > 0 && $this->hash !== false;
    }
}
