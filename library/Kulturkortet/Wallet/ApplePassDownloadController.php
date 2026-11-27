<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\AppleWallet\ApplePassDataStore;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use WpService\WpService;

/** Serves short-lived Apple Wallet pass downloads. */
class ApplePassDownloadController
{
    /**
     * @param WpService $wpService WordPress query-var and redirect services.
     * @param ApplePassDataStore $applePassDataStore Consumes single-use tokens.
     * @param TicketApplePassGenerator $applePassGenerator Creates signed Apple passes.
     */
    public function __construct(
        private WpService $wpService,
        private ApplePassDataStore $applePassDataStore,
        private TicketApplePassGenerator $applePassGenerator,
    ) {}

    /**
     * Streams a pass or returns expired links to the ticket page.
     *
     * @param MunicipioAuthNavigationInterface $navigation Builds the current ticket page URL.
     * @return void
     */
    public function handle(MunicipioAuthNavigationInterface $navigation): void
    {
        $token = $this->wpService->getQueryVar(WalletService::APPLE_PASS_QUERY_VAR);
        if (!is_string($token) || preg_match('/^[a-f0-9]{64}$/', $token) !== 1) {
            return;
        }

        $this->wpService->nocacheHeaders();
        $passData = $this->applePassDataStore->consume($token);
        if (!is_array($passData)) {
            $ticketUrl = $navigation->getModifiedHomeUrl(
                removeQueryArgs: [WalletService::APPLE_PASS_QUERY_VAR],
                addQueryArgs: [WalletService::APPLE_PASS_EXPIRED_QUERY_VAR => '1'],
            );
            if ($this->wpService->wpSafeRedirect($ticketUrl)) {
                exit;
            }
            return;
        }

        try {
            $pass = $this->applePassGenerator->generate($passData);
        } catch (\Throwable $exception) {
            error_log('Unable to generate Apple Wallet pass: ' . $exception->getMessage());
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Unable to create the Apple Wallet pass. Please reload the ticket page and try again.';
            exit;
        }

        header('Content-Type: application/vnd.apple.pkpass');
        header('Content-Disposition: attachment; filename="kulturkortet.pkpass"');
        header('Content-Length: ' . strlen($pass));
        echo $pass;
        exit;
    }
}
