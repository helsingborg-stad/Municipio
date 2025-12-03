<?php

namespace Municipio\PostObject\Icon\Resolvers;

use AcfService\Contracts\GetField;
use Municipio\PostObject\{PostObjectInterface, Icon\Icon, Icon\IconInterface};

/**
 * Term icon resolver.
 */
class PostIconResolver implements IconResolverInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private GetField $acfService,
        private IconResolverInterface $innerResolver
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): ?IconInterface
    {
        $dbValue = $this->acfService->getField('icon', $this->postObject->getId());

        if ($this->isSvgIcon($dbValue)) {
            return Icon::create([
                'icon'        => $dbValue['icon']['svg']['url'] ?? false,
                'customColor' => $dbValue['color'] ?? false,
            ]);
        } elseif ($this->isMaterialIcon($dbValue)) {
            return Icon::create([
                'icon'        => $dbValue['icon']['material_icon'] ?? false,
                'customColor' => $dbValue['color'] ?? false,
            ]);
        }

        return $this->innerResolver->resolve();
    }

    /**
     * Check if value is an SVG icon.
     *
     * @param array $value
     * @return bool
     */
    private function isSvgIcon($value): bool
    {
        if (empty($value) || !is_array($value)) {
            return false;
        }

        if (!isset($value['icon']['type']) || $value['icon']['type'] !== 'svg') {
            return false;
        }

        if (!isset($value['icon']['svg']) || !isset($value['icon']['svg']['url'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if value is a material icon.
     *
     * @param array $value
     * @return bool
     */
    private function isMaterialIcon($value): bool
    {
        if (empty($value) || !is_array($value)) {
            return false;
        }

        if (!isset($value['icon']['type']) || $value['icon']['type'] !== 'icon') {
            return false;
        }

        if (!isset($value['icon']) || empty($value['icon']['material_icon'])) {
            return false;
        }

        return true;
    }
}
