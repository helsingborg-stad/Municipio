<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use Municipio\KulturkortetQRCodeViewer\Vitec\VitecServiceInterface;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

class KulturkortetAuthViewFactory implements MunicipioAuthViewFactoryInterface
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
            return $this->renderWithModel('kulturkortet-no-vitec-user', ['model' => ['logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']), 'name' => $user->getName()]]);
        }

        return $this->renderWithModel('kulturkortet-vitec-user', [
            'model' => [
                'logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
                'name' => $user->getName(),
                'email' => $ticket['email'] ?? null,
                'barcode' => $ticket['barcode'] ?? null,
                'validFrom' => $this->formatDate($ticket['validFrom'] ?? null),
                'validTo' => $this->formatDate($ticket['validUntil'] ?? null),
            ],
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
        return $this->renderWithModel('kulturkortet-anonymous', ['url' => $loginUrl]);
    }

    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-logged-out', ['name' => $user->getName(), 'url' => $navigation->getHomeUrl()]);
    }

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-error', ['error' => $error, 'url' => $navigation->getHomeUrl()]);
    }

    private function renderWithModel(string $template, array $model): string
    {
        $bsf = new BladeServiceFactory($this->wpService);
        $bladeRenderer = new BladeRenderer($bsf->create([self::getTemplateDir()]));

        return $bladeRenderer->render($template, [
            'heading' => $this->attributes['heading'] ?? '',
            'content' => $this->attributes['content'] ?? '',
            'buttonText' => $this->attributes['buttonText'] ?? '',
            ...$model,
        ]);
    }

    private function formatDate(mixed $date): ?string
    {
        $time = is_string($date) ? strtotime($date) : null;
        return $time ? date(self::DATE_FORMAT, $time) : null;
    }
}
