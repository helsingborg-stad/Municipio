<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\ProfileEditor;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\Helper\DateFormat;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUserInterface;
use Municipio\Kulturkortet\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;
use Municipio\Kulturkortet\Vitec\VitecServiceInterface;
use WpService\Contracts\__;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

class KulturkortetProfileEditorAuthViewFactory implements MunicipioAuthViewFactoryInterface
{
    public function __construct(
        private WpCacheGet&WpCacheSet&__ $wpService,
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
                    'heading' => $this->wpService->__('You do not have a valid kulturkort', 'municipio'),
                    'content' => '',
                    'actionLabel' => $this->wpService->__('Logout', 'municipio'),
                ],
                'url' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            ]);
        }

        return $this->renderWithModel('kulturkortet-profile-editor', [
            'lang' => [
                'heading' => $this->wpService->__('Your details', 'municipio'),
                'content' => '',
                'logoutUrl' => $this->wpService->__('Logout', 'municipio'),
                'saveUrl' => $this->wpService->__('Save', 'municipio'),
                'firstnameLabel' => $this->wpService->__('First name', 'municipio'),
                'firstnamePlaceholder' => $this->wpService->__('Enter your first name', 'municipio'),
                'lastnameLabel' => $this->wpService->__('Last name', 'municipio'),
                'lastnamePlaceholder' => $this->wpService->__('Enter your last name', 'municipio'),
                'emailLabel' => $this->wpService->__('Email', 'municipio'),
                'emailPlaceholder' => $this->wpService->__('Enter your email', 'municipio'),
                'myTicket' => $this->wpService->__('My ticket', 'municipio'),
                'activeUntil' => $this->wpService->__('Active until', 'municipio'),
            ],
            'profile' => [
                'firstname' => $ticket['firstname'] ?? '',
                'lastname' => $ticket['lastname'] ?? '',
                'email' => $ticket['email'] ?? '',
            ],
            'name' => $user->getName() ?? '',
            'saveUrl' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
            'logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            'ticket' => [
                'validUntil' => $this->formatDate($ticket['validUntil'] ?? null),
            ],
            //Debug remove later
            'showDebugInfo' => defined('KULTURKORTET_DEBUG') && KULTURKORTET_DEBUG,
            'vitecUser' => $vitecUser,
        ]);
    }

    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-profile-simple-message', [
            'lang' => [
                'heading' => $this->wpService->__('Login to edit your profile', 'municipio'),
                'content' => $this->wpService->__('To edit your profile, you must log in with BankId.', 'municipio'),
                'actionLabel' => $this->wpService->__('Log in', 'municipio'),
            ],
            'url' => $loginUrl,
        ]);
    }

    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation, ?string $loginUrl = null): string
    {
        return $this->renderWithModel('kulturkortet-profile-simple-message', [
            'lang' => [
                'heading' => $this->wpService->__('You have been successfully logged out', 'municipio'),
                'content' => $this->wpService->__('You have been successfully logged out from your kulturkort and BankId.', 'municipio'),
                'actionLabel' => $this->wpService->__('Start over', 'municipio'),
            ],
            'url' => $loginUrl ?? $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
        ]);
    }

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation, ?string $loginUrl = null): string
    {
        return $this->renderWithModel('kulturkortet-profile-simple-message', [
            'lang' => [
                'heading' => $this->wpService->__('Something went wrong', 'municipio'),
                'content' => $error,
                'actionLabel' => $this->wpService->__('Try again', 'municipio'),
            ],
            'url' => $loginUrl ?? $navigation->getHomeUrl(),
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
        return $time ? date(DateFormat::getDateFormat('date'), $time) : null;
    }
}
