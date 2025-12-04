<?php

namespace Municipio\ImageFocus\Resolvers;

class ChainFocusPointResolver implements FocusPointResolverInterface
{
    /**
     * @var FocusPointResolverInterface[]
     */
    private array $resolvers;

    public function __construct(FocusPointResolverInterface ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function isSupported(): bool
    {
        return true; // Always allowed to run, individual resolvers will check support
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        foreach ($this->resolvers as $resolver) {
            if (!$resolver->isSupported()) {
                continue;
            }
            $result = $resolver->resolve($filePath, $width, $height, $attachmentId);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
}
