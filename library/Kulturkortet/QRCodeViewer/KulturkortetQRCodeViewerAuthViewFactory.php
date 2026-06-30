<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\QRCodeViewer;

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

class KulturkortetQRCodeViewerAuthViewFactory implements MunicipioAuthViewFactoryInterface
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
        $ticket = $this->vitecService->tryGetTicket($user->getSSN());

        if (!$ticket) {
            return $this->renderWithModel('kulturkortet-qr-simple', [
                'lang' => [
                    'heading' => $this->wpService->__('Your Kulturkort', 'municipio'),
                    'content' => '',
                    'actionLabel' => $this->wpService->__('Log out', 'municipio'),
                ],
                'url' => $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
                'name' => $user->getName(),
                'notice' => [
                    'type' => 'info',
                    'message' =>[
                        'text' => $this->wpService->__('You do not have a valid Kulturkort.', 'municipio')
                    ],
                    'icon' => [
                        'name' => 'info',
                        'size' => 'md'
                    ]
                ]
            ]);
        }

        return $this->renderWithModel('kulturkortet-vitec-user', [
            'lang' => [
                'days' => $this->wpService->__('Days', 'municipio'),
                'yourCultureCard' => $this->wpService->__('Your Kulturkort', 'municipio'),
            ],
            'actions' => $this->getActions($navigation, true),
            'profile' => [
                'firstname' => $ticket['firstname'] ?? '',
                'lastname' => $ticket['lastname'] ?? '',
            ],
            'ticket' => [
                'barcode' => $ticket['barcode'] ?? null,
                'validFrom' => $this->formatDate($ticket['validFrom'] ?? null),
                'validTo' => $this->formatDate($ticket['validUntil'] ?? null),
                'daysLeft' => $this->calculateDaysLeft($ticket['validUntil'] ?? null),
            ]
        ]);
    }

    public function whenAnonymous(string $loginUrl, MunicipioAuthNavigationInterface $navigation): string
    {
        return $this->renderWithModel('kulturkortet-qr-simple', [
            'lang' => [
                'heading' => $this->wpService->__('Log in to view your kulturkort', 'municipio'),
                'content' => $this->wpService->__('To view your kulturkort, you must log in with BankId.', 'municipio'),
                'actionLabel' => $this->wpService->__('Log in', 'municipio'),
            ],
            'url' => $loginUrl,
        ]);
    }

    public function whenLogOut(MunicipioAuthenticatedUserInterface $user, MunicipioAuthNavigationInterface $navigation, ?string $loginUrl = null): string
    {
        $loginUrl = $loginUrl ?? $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']);

        return $this->renderWithModel('kulturkortet-qr-simple', [
            'lang' => [
                'heading' => $this->wpService->__('You have been successfully logged out', 'municipio'),
                'content' => $this->wpService->__('You have been successfully logged out from your kulturkort and BankId.', 'municipio'),
                'actionLabel' => $this->wpService->__('Login again', 'municipio'),
            ],
            'url' => $loginUrl ?? $navigation->getHomeUrl(),
        ]);
    }

    public function whenError(string $error, MunicipioAuthNavigationInterface $navigation, ?string $loginUrl = null): string
    {
        $loginUrl = $loginUrl ?? $navigation->getModifiedHomeUrl(removeQueryArgs: ['action']);

        return $this->renderWithModel('kulturkortet-qr-simple', [
            'lang' => [
                'heading' => $this->wpService->__('An error occurred', 'municipio'),
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

    private function getActions(MunicipioAuthNavigationInterface $navigation, bool $hasValidKulturkort = false): array
    {
        $actions = [];
        if (!empty($this->attributes['profileLink'] ?? '')) {
            $actions[] = ActionCreator::create(
                $this->wpService->__('Profile', 'municipio'),
                $this->attributes['profileLink'],
                'person'
            );
        }

        if (!empty($this->attributes['renewLink'] ?? '')) {
            $actions[] = ActionCreator::create(
                $hasValidKulturkort ? $this->wpService->__('Renew', 'municipio') : $this->wpService->__('Buy', 'municipio'),
                $this->attributes['renewLink'],
                'shopping_cart'
            );
        }

        $actions[] = ActionCreator::create(
            $this->wpService->__('Logout', 'municipio'),
            $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']),
            'logout'
        );

        return $actions;
    }

    /**
     * Calculates remaining whole calendar days until ticket expiry date.
     *
     * Returns null for invalid date input, and clamps expired tickets to 0 days.
     *
     * @param mixed $validUntil The ticket valid-until datetime string.
     * @return int|null Number of days left, or null when input is invalid.
     */
    private function calculateDaysLeft(mixed $validUntil): ?int
    {
        $expiryTimestamp = is_string($validUntil) ? strtotime($validUntil) : false;

        if ($expiryTimestamp === false) {
            return null;
        }

        $currentDate = (new \DateTimeImmutable())->setTime(0, 0, 0);
        $expiryDate = (new \DateTimeImmutable('@' . $expiryTimestamp))
            ->setTimezone($currentDate->getTimezone())
            ->setTime(0, 0, 0);

        $daysLeft = (int) $currentDate->diff($expiryDate)->format('%r%a');

        return max(0, $daysLeft);
    }
}
