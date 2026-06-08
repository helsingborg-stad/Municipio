<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemotePost;
use WpService\Contracts\WpRemoteRetrieveBody;

// Vitec API: https://helsingborg.entryevent.se/kulturkortet/swagger/index.html
class VitecService implements VitecServiceInterface
{
    public function __construct(
        private IsWpError&WpRemoteGet&WpRemotePost&WpRemoteRetrieveBody $wpService,
        private VitecConfigInterface $config,
    ) {}

    public function tryGetTicket(string $ssn): ?array
    {
        // For testing purposes, allow overriding the SSN with a known cardholder
        $url = $this->config->getBaseUrl() . '/kulturkortet/customer/' . VitecSSN::formatSSN($ssn) . '/tickets';

        $response = $this->wpService->wpRemoteGet($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getApiKey(),
            ],
        ]);
        if ($this->wpService->isWpError($response)) {
            return null;
        }
        $body = $this->wpService->wpRemoteRetrieveBody($response);

        $decodedBody = json_decode($body, true, flags: JSON_OBJECT_AS_ARRAY);

        $ticket = array_values(
            array_filter(
                $decodedBody['tickets'] ?? [],
                fn($t) => $t['ticketTemplateName'] === 'Import_Kulturkort')
            )[0] ?? null;

        return $ticket ?? null;
    }

    public function updateUserData(string $ssn, string $email): ?array
    {
        $ticket = $this->tryGetTicket($ssn);

        if (!$ticket) {
            return null;
        }

        $url = $this->config->getBaseUrl() . '/kulturkortet/customer/' . $ticket['id'] . '/update';
        $response = $this->wpService->wpRemotePost($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getApiKey(),
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'email' => $email,
                'civicRegistrationNumber' => $ticket['civicRegistrationNumber'] ?? '',
                'firstname' => $ticket['firstname'] ?? '',
                'lastname' => $ticket['lastname'] ?? '',
            ]),
        ]);
        if ($this->wpService->isWpError($response)) {
            return null;
        }
        $body = $this->wpService->wpRemoteRetrieveBody($response);
        return json_decode($body, true, flags: JSON_OBJECT_AS_ARRAY);
    }
}
