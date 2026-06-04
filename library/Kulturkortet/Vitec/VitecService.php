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
        $actualSsn = defined('KULTURKORTET_VITEC_SSN') ? KULTURKORTET_VITEC_SSN : $ssn;

        $url = $this->config->getBaseUrl() . '/kulturkortet/customer/' . VitecSSN::formatSSN($actualSsn) . '/tickets';

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

        //returns something similar to
        // {
        //         "version":25860271,
        //         "tickets":[
        //                 {
        //                         "id":"XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
        //                         "barcode":"1234abcd",
        //                         "tagId":"1234abcd",
        //                         "civicRegistrationNumber":"19700101-0000",
        //                         "validFrom":"2024-12-05T00:00:00",
        //                         "validUntil":"2026-12-11T23:59:00",
        //                         "firstname":"Test",
        //                         "lastname":"Testersson",
        //                         "email":"test@example.com",
        //                         "articleName":"Kulturkort\/Nyf\u00f6rs\u00e4ljning",
        //                         "ticketTemplateName":"Import_Kulturkort",
        //                         "plu":1300,
        //                         "saleDate":"2024-12-05T12:03:59.296",
        //                         "statisticsValues":{
        //                             "ANL\u00c4GGNINGSBES\u00d6K":"- Ej applicerbar",
        //                             "F\u00f6rs\u00e4ljning":"Kulturkort",
        //                             "Kategorigrupp":"Betalande",
        //                             "Rapportgrupp":"Endast entr\u00e9",
        //                             "Rapportkategori":"Kulturkortsbes\u00f6k",
        //                             "Verksamhet":"- Ej applicerbar"
        //                         },
        //                         "timestamp":"2025-12-11T12:09:52.9416901",
        //                         "version":25860271,
        //                         "oldCardRef":null,
        //                         "isCancelled":false,
        //                         "hasBlock":false
        //                 }
        //         ]
        // }
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
