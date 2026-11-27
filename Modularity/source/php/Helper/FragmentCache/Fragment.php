<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

/**
 * Rendered markup of one module placement, with the assets it needs.
 */
final class Fragment
{
    /**
     * @param array<mixed> $assets Asset context to restore when the markup is replayed
     */
    public function __construct(
        public readonly string $html,
        public readonly array $assets,
    ) {}

    /**
     * @return array{html: string, assets: array<mixed>}
     */
    public function toArray(): array
    {
        return ['html' => $this->html, 'assets' => $this->assets];
    }

    public static function fromArray(mixed $data): ?self
    {
        if (!is_array($data) || !is_string($data['html'] ?? null) || !is_array($data['assets'] ?? null)) {
            return null;
        }

        return new self($data['html'], $data['assets']);
    }
}
