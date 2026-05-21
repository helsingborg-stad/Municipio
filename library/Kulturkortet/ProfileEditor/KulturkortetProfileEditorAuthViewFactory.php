<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\ProfileEditor;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\Kulturkortet\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use Municipio\Kulturkortet\Vitec\VitecServiceInterface;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

class KulturkortetProfileEditorAuthViewFactory implements MunicipioAuthViewFactoryInterface
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

    public function whenAuthenticated(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
    {
        if ($_POST['action'] ?? null === 'save') {
            $newEmail = $_POST['email'] ?? '';
            try {
                $this->vitecService->updateUserData($user->getSSN(), $newEmail);
                // Redirect to avoid resubmission
                header('Location: ' . $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'saved']));
                exit();
            } catch (\Exception $e) {
                return $this->whenError($e->getMessage(), $navigation);
            }
        }

        $vitecUser = $this->vitecService->tryGetUserData($user->getSSN());

        $ticket = $vitecUser['tickets'][0] ?? null;
        if (!$ticket) {
            return $this->renderWithModel('kulturkortet-profile-simple-message', [
                'lang' => [
                    'heading' => __('Du verkar inte ha ett giltigt kulturkort', 'municipio'),
                    'content' => '',
                    'url' => __('Logga ut', 'municipio'),
                ],
                'url' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            ]);
        }

        return $this->renderWithModel('kulturkortet-profile-editor', [
            'lang' => [
                'heading' => __('Dina uppgifter', 'municipio'),
                'content' => '',
                'logoutUrl' => __('Logga ut', 'municipio'),
                'saveUrl' => __('Spara', 'municipio'),

                'emailLabel' => __('E-post', 'municipio'),
                'emailPlaceholder' => __('Ange din e-post', 'municipio'),
            ],
            'profile' => [
                'name' => $user->getName() ?? '',
                'email' => $ticket['email'] ?? '',
            ],
            'vitecUser' => $vitecUser,
            'saveUrl' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
            'logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
        ]);
    }

    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-profile-simple-message', [
            'lang' => [
                'heading' => __('Logga in för att redigera din profil', 'municipio'),
                'content' => __('För att kunna redigera din profil du logga in med BankId.', 'municipio'),
                'url' => __('Logga in', 'municipio'),
            ],
            'url' => $loginUrl,
        ]);
    }

    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-profile-simple-message', [
            'lang' => [
                'heading' => __('Du är nu utloggad', 'municipio'),
                'content' => __('', 'municipio'),
                'url' => __('Starta om', 'municipio'),
            ],
            'url' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
        ]);
    }

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-profile-simple-message', [
            'lang' => [
                'heading' => __('Något gick fel', 'municipio'),
                'content' => $error,
                'url' => __('Prova igen', 'municipio'),
            ],
            'url' => $navigation->getHomeUrl(),
        ]);
    }

    private function renderWithModel(string $template, array $model): string
    {
        $bsf = new BladeServiceFactory($this->wpService);
        $bladeRenderer = new BladeRenderer($bsf->create([self::getTemplateDir()]));

        return $bladeRenderer->render($template, [
            ...$model,
        ]);
    }
}
