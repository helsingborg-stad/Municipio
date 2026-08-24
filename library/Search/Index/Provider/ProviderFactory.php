<?php

namespace Municipio\Search\Index\Provider;

class ProviderFactory
{
    private const DEFAULT_PROVIDER = 'algolia';

    public static function getProviders()
    {
        return apply_filters('AlgoliaIndex/Provider/Factory', [
            'algolia' => fn() => \Municipio\Search\Index\Provider\Algolia\AlgoliaFactory::createFromEnv(),
        ]);
    }

    public static function createFromEnv($provider = null): AbstractProvider
    {
        $providers = self::getProviders();

        $provider = !empty($provider) ? $provider : get_field('algolia_index_search_provider', 'option') ?? self::DEFAULT_PROVIDER;

        if (!is_string($provider)) {
            throw new \InvalidArgumentException('Provider name must be a string');
        }

        if (!array_key_exists($provider, $providers)) {
            $provider = self::DEFAULT_PROVIDER;
        }

        if (!is_callable($providers[$provider])) {
            throw new \InvalidArgumentException('Provider is not callable');
        }

        $factory = $providers[$provider];

        return $factory();
    }
}
