<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\ProfileEditor;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\Helper\DateFormat;
use Municipio\Kulturkortet\Helper\ActionCreator;
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

        $ticket = $this->vitecService->tryGetTicket($user->getSSN());

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
                'emailLabel' => $this->wpService->__('Email', 'municipio'),
                'emailPlaceholder' => $this->wpService->__('Enter your email', 'municipio'),
                'activeUntil' => $this->wpService->__('Active until', 'municipio'),
                'profileSaved' => $this->wpService->__('Your profile was successfully updated', 'municipio'),
            ],
            'showSavedNotice' => isset($_GET['action']) && $_GET['action'] === 'saved',
            'profile' => [
                'firstname' => $ticket['firstname'] ?? '',
                'lastname' => $ticket['lastname'] ?? '',
                'email' => $ticket['email'] ?? '',
            ],
            'saveUrl' => $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']),
            'actions' => $this->getActions($navigation),
            'logoutUrl' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            'ticket' => [
                'validUntil' => $this->formatDate($ticket['validUntil'] ?? null),
            ]
        ]);
    }

    private function getActions(MunicipioAuthNavigationInterface $navigation): array
    {
        $actions = [];

        if (!empty($this->attributes['ticketLink'])) {
            $actions[] = ActionCreator::create(
                $this->wpService->__('My ticket', 'municipio'),
                $this->attributes['ticketLink'],
                'confirmation_number'
            );
        }

        $actions[] = ActionCreator::create(
            $this->wpService->__('Logout', 'municipio'),
            $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            'logout'
        );

        return $actions;
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
