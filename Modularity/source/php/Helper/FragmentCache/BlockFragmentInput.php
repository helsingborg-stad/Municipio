<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

/**
 * The part of a block that identifies its fragment.
 *
 * ACF passes the block type merged with the block's attributes, so the block also
 * carries objects such as the render callback instance. Their state changes while
 * the page renders and must not change the fragment hash. Only plain values are kept.
 */
final class BlockFragmentInput
{
    /**
     * @param array<array-key, mixed> $block
     * @return array<array-key, mixed>
     */
    public static function fromBlock(array $block): array
    {
        $input = [];

        foreach ($block as $key => $value) {
            if (is_object($value)) {
                continue;
            }

            $input[$key] = is_array($value) ? self::fromBlock($value) : $value;
        }

        return $input;
    }
}
