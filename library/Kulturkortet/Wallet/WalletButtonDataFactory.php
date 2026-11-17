<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;

/** Creates the enabled Wallet-button URLs for the ticket view. */
class WalletButtonDataFactory
{
    /**
     * @param WalletService $appleWalletService Creates Apple pass links.
     * @param GoogleWalletLinkFactory $googleWalletLinkFactory Creates Google Wallet links.
     */
    public function __construct(
        private WalletService $appleWalletService,
        private GoogleWalletLinkFactory $googleWalletLinkFactory,
    ) {}

    /**
     * Builds Wallet button data for an active ticket.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param MunicipioAuthNavigationInterface $navigation Builds the Apple pass URL.
     * @return array<string, string> Available Wallet URLs keyed by provider.
     */
    public function create(array $ticket, MunicipioAuthNavigationInterface $navigation): array
    {
        $wallet = [];
        try {
            $applePassUrl = $this->appleWalletService->createApplePassUrl($ticket, $navigation);
            if ($applePassUrl !== null) {
                $wallet['apple'] = $applePassUrl;
            }
        } catch (\Exception $exception) {
            error_log('Unable to create Apple Wallet link: ' . $exception->getMessage());
        }

        try {
            $googleWalletLink = $this->googleWalletLinkFactory->createLink($ticket);
            if ($googleWalletLink !== null) {
                $wallet['google'] = $googleWalletLink;
            }
        } catch (\Exception $exception) {
            error_log('Unable to create Google Wallet link: ' . $exception->getMessage());
        }

        return $wallet;
    }
}
