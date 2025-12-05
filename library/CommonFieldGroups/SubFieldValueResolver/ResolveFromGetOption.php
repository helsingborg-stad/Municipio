<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

use WpService\Contracts\GetOption;

/**
 * Class ResolveFromGetOption
 *
 * Resolves the value from a get_option call.
 */
class ResolveFromGetOption implements SubFieldValueResolverInterface
{
    /**
     * Constructor.
     *
     * @param GetOption $wpService
     * @param SubFieldValueResolverInterface $innerResolver
     */
    public function __construct(
        private GetOption $wpService,
        private SubFieldValueResolverInterface $innerResolver = new NullResolver()
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(array $subField, string $subFieldKey): mixed
    {
        return $this->wpService->getOption($subFieldKey) ?? $this->innerResolver->resolve($subField, $subFieldKey);
    }
}
