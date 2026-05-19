<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use Municipio\KulturkortetQRCodeViewer\Vitec\VitecServiceInterface;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

class KulturkortetAuthViewFactory implements MunicipioAuthViewFactoryInterface
{
    public function __construct(
        private WpCacheGet&WpCacheSet $wpService,
        private VitecServiceInterface $vitecService,
        private array $attributes = [],
    ) {}

    public static function getTemplateDir(): string
    {
        return __DIR__ . '/views';
    }

    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user): string
    {
        $vitecUser = $this->vitecService->tryGetUserData($user->getSSN());
        if ($vitecUser === null) {
            return $this->renderWithModel('kulturkortet-no-vitec-user', ['user' => $user]);
        }
        return $this->renderWithModel('kulturkortet-vitec-user', ['user' => $user, 'vitecUser' => $vitecUser]);
    }

    public function whenAnonymous(string $redirectUrl): string
    {
        return $this->renderWithModel('kulturkortet-anonymous', ['url' => $redirectUrl]);
    }

    public function whenError(string $error, string $redirectUrl): string
    {
        return $this->renderWithModel('kulturkortet-error', ['error' => $error, 'url' => $redirectUrl]);
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
}
