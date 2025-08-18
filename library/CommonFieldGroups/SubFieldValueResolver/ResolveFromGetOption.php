<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

use WpService\Contracts\GetOption;

class ResolveFromGetOption implements SubFieldValueResolverInterface
{
    public function __construct(
        private GetOption $wpService,
        private SubFieldValueResolverInterface $innerResolver = new NullResolver()
    ) {
    }

    public function resolve(array $subField, string $subFieldKey): mixed
    {
        return $this->wpService->getOption($subFieldKey) ?? $this->innerResolver->resolve($subField, $subFieldKey);
    }
}
