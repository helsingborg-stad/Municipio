<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\Vitec;

use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;

class VitecService implements VitecServiceInterface
{
    public function __construct(
        private IsWpError&WpRemoteGet&WpRemoteRetrieveBody $wpService,
        private VitecConfigInterface $config = new VitecConfig(),
    ) {}

    public function tryGetUserData(string $ssn): ?array
    {
        $url = defined('KULTURKORTET_VITEC_SSN') ? $this->config->getBaseUrl() . '/kulturkortet/customer/' . KULTURKORTET_VITEC_SSN . '/tickets' : $this->config->getBaseUrl() . '/kulturkortet/customer/' . $ssn . '/tickets';

        $response = $this->wpService->wpRemoteGet($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getApiKey(),
            ],
        ]);
        if ($this->wpService->isWpError($response)) {
            return null;
        }
        $body = $this->wpService->wpRemoteRetrieveBody($response);
        return json_decode($body, true);
    }
}
