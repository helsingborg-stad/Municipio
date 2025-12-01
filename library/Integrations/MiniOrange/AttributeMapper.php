<?php

namespace Municipio\Integrations\MiniOrange;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Municipio\Integrations\MiniOrange\Provider\ProviderInterface;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class AttributeMapper implements Hookable
{
    private ?ProviderInterface $provider = null;

    public function __construct(private WpService $wpService, private MiniOrangeConfig $config, ProviderInterface ...$provider)
    {
        foreach ($provider as $provider) {
            if ($provider->identifier() === $config->getCurrentProvider()) {
                $this->provider = $provider;
                break;
            }
        }
    }

  /**
   * Add hooks to map the attributes from the idefied provider
   *
   * @return void
   */
    public function addHooks(): void
    {
        if ($this->provider === null) {
            return;
        }
        foreach ($this->provider->getMap() as $key => $value) {
            $this->wpService->addFilter('default_option_' . $key, function () use ($value) {
                return $value;
            }, 1);
        }
    }
}
