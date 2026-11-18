<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use WpService\WpService;

/** Serves short-lived Apple Wallet pass downloads. */
class ApplePassDownloadController
{
    /**
     * @param WpService $wpService WordPress query-var and transient services.
     * @param ApplePassGenerator $applePassGenerator Creates signed Apple passes.
     */
    public function __construct(
        private WpService $wpService,
        private ApplePassDataStore $applePassDataStore,
        private ApplePassGenerator $applePassGenerator,
    ) {}

    /**
     * Streams a pass for a valid one-time token and returns for ordinary requests.
     *
     * @return void
     */
    public function handle(): void
    {
        $token = $this->wpService->getQueryVar(WalletService::APPLE_PASS_QUERY_VAR);
        if (!is_string($token) || preg_match('/^[a-f0-9]{64}$/', $token) !== 1) {
            return;
        }

        header('Cache-Control: no-store, private, max-age=0');
        header('Pragma: no-cache');
        $passData = $this->applePassDataStore->consume($token);
        if (!is_array($passData)) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'This Apple Wallet pass link has expired or has already been used.';
            exit;
        }

        $pass = $this->applePassGenerator->generate($passData);
        header('Content-Type: application/vnd.apple.pkpass');
        header('Content-Disposition: attachment; filename="kulturkortet.pkpass"');
        header('Content-Length: ' . strlen($pass));
        echo $pass;
        exit;
    }
}
