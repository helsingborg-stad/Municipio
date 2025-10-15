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

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        foreach ($this->resolvers as $resolver) {
            $result = $resolver->resolve($filePath, $width, $height, $attachmentId);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
}
