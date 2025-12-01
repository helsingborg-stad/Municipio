<?php

declare(strict_types=1);

namespace Municipio\SingleDigitalGateway;

use Municipio\Schema\SingleDigitalGateway;

class GetMetaTagsFromSchema
{
    public function __construct(
        private SingleDigitalGateway $schema,
    ) {}

    /**
     * Get meta tags from SingleDigitalGateway schema
     *
     * @return MetaTag[]
     */
    public function getMetaTags(): array
    {
        $metaTags = [];
        $this->addPolicyCode($metaTags);
        $this->addService($metaTags);
        $this->addPolicy($metaTags);
        $this->addLocation($metaTags);

        if (count($metaTags) > 0) {
            $this->addStaticMetaTags($metaTags);
        }

        return $metaTags;
    }

    private function addPolicyCode(array &$metaTags): void
    {
        $policyCode = $this->schema->getProperty('policyCode');
        if ($policyCode) {
            $metaTags[] = new MetaTag('policy-code', $policyCode);
        }
    }

    private function addService(array &$metaTags): void
    {
        $service = $this->schema->getProperty('service');
        if ($service) {
            if (is_string($service)) {
                $metaTags[] = new MetaTag('DC.Service', $service);
            }
            if (is_array($service)) {
                $metaTags[] = new MetaTag('DC.Service', implode(';', $service));
            }
        }
    }

    private function addPolicy(array &$metaTags): void
    {
        $policy = $this->schema->getProperty('policy');
        if ($policy) {
            $metaTags[] = new MetaTag('DC.Policy', $policy);
        }
    }

    private function addLocation(array &$metaTags): void
    {
        $location = $this->schema->getProperty('location');
        if ($location) {
            $metaTags[] = new MetaTag('DC.Location', $location);
        }
    }

    private function addStaticMetaTags(array &$metaTags): void
    {
        $metaTags[] = new MetaTag('sdg-tag', 'sdg');
        $metaTags[] = new MetaTag('DC.ISO3166', 'SE');
        $metaTags[] = new MetaTag('DC.Language', 'en');
    }
}
