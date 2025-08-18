<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

use WpService\Contracts\GetOption;

class ResolveValueFromSelectFieldThatReturnsBothLabelAndValue implements SubFieldValueResolverInterface
{
    public function __construct(
        private GetOption $wpService,
        private SubFieldValueResolverInterface $innerResolver = new NullResolver()
    ) {
    }

    public function resolve(array $subField, string $subFieldKey): mixed
    {
        if ($subField['type'] !== 'select' || $subField['return_format'] !== 'array') {
            return $this->innerResolver->resolve($subField, $subFieldKey);
        }

        $optionValue = $this->wpService->getOption($subFieldKey);

        if (empty($optionValue)) {
            return $this->innerResolver->resolve($subField, $subFieldKey);
        }

        if (!array_key_exists($optionValue, $subField['choices'])) {
            return $this->innerResolver->resolve($subField, $subFieldKey);
        }

        return [
            'label' => $subField['choices'][$optionValue] ?? null,
            'value' => $optionValue,
        ];
    }
}
