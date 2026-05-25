<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\QRCodeViewer;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\Kulturkortet\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use Municipio\Kulturkortet\Vitec\VitecServiceInterface;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

class KulturkortetQRCodeViewerAuthViewFactory implements MunicipioAuthViewFactoryInterface
{
    private const DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private WpCacheGet&WpCacheSet $wpService,
        private VitecServiceInterface $vitecService,
        private array $attributes = [],
    ) {}

    public static function getTemplateDir(): string
    {
        return __DIR__ . '/views';
    }

    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
    {
        $vitecUser = $this->vitecService->tryGetUserData($user->getSSN());

        $ticket = $vitecUser['tickets'][0] ?? null;
        if (!$ticket) {
            return $this->renderWithModel('kulturkortet-qr-simple', [
                'lang' => [
                    'heading' => __('Du verkar inte ha ett giltigt kulturkort', 'municipio'),
                    'content' => '',
                    'url' => __('Logga ut', 'municipio'),
                ],
                'url' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
                'name' => $user->getName(),
            ]);
        }

        return $this->renderWithModel('kulturkortet-vitec-user', [
            'lang' => [],

            'logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            'name' => $user->getName(),
            'profile' => [
                'firstname' => $ticket['firstname'] ?? '',
                'lastname' => $ticket['lastname'] ?? '',
                'email' => $ticket['email'] ?? '',
            ],
            'ticket' => [
                'barcode' => $ticket['barcode'] ?? null,
                'validFrom' => $this->formatDate($ticket['validFrom'] ?? null),
                'validTo' => $this->formatDate($ticket['validUntil'] ?? null),
            ],
            'showDebugInfo' => defined('KULTURKORTET_DEBUG') && KULTURKORTET_DEBUG, // set to true to show raw Vitec user data for debugging purposes
            'vitecUser' => $vitecUser, // for debugging purposes
        ]);

        // $vitecUser is expected to look something like
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

    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-qr-simple', [
            'lang' => [
                'heading' => __('Logga in för att se ditt kulturkort', 'municipio'),
                'content' => __('För att kunna se ditt kulturkort du logga in med BankId.', 'municipio'),
                'url' => __('Logga in', 'municipio'),
            ],
            'url' => $loginUrl,
        ]);
    }

    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-qr-simple', [
            'lang' => [
                'heading' => __('Du har loggat ut', 'municipio'),
                'content' => __('Du har loggat ut från ditt kulturkort.', 'municipio'),
                'url' => __('Till startsidan', 'municipio'),
            ],
            'url' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
        ]);
    }

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-qr-simple', [
            'lang' => [
                'heading' => __('Ett fel uppstod', 'municipio'),
                'content' => $error,
                'url' => __('Till startsidan', 'municipio'),
            ],
            'url' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
        ]);
    }

    private function renderWithModel(string $template, array $model): string
    {
        $bsf = new BladeServiceFactory($this->wpService);
        $bladeRenderer = new BladeRenderer($bsf->create([self::getTemplateDir()]));

        return $bladeRenderer->render($template, [
            'attributes' => $this->attributes,
            ...$model,
        ]);
    }

    private function formatDate(mixed $date): ?string
    {
        $time = is_string($date) ? strtotime($date) : null;
        return $time ? date(self::DATE_FORMAT, $time) : null;
    }
}
