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
        if ($vitecUser === null) {
            return $this->renderWithModel('kulturkortet-no-vitec-user', ['model' => ['logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']), 'name' => $user->getName()]]);
        }
        return $this->renderWithModel('kulturkortet-vitec-user', [
            // 'user' => $user,
            // 'vitecUser' => $vitecUser,
            'model' => [
                'logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
                'name' => $user->getName(),
                'barcode' => $vitecUser['tickets'][0]['barcode'] ?? null,
                'validFrom' => $this->formatDate($vitecUser['tickets'][0]['validFrom'] ?? null),
                'validTo' => $this->formatDate($vitecUser['tickets'][0]['validUntil'] ?? null),
            ],
        ]);
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
