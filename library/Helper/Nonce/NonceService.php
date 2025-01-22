<?php

namespace Municipio\Helper\Nonce;

use WpService\Contracts\WpCreateNonce;
use WpService\Contracts\WpVerifyNonce;

class NonceService implements NonceServiceInterface
{
    public const DEFAULT_ACTION = 'municipio_action';
    public const DEFAULT_NAME   = '_municipio_nonce';

    /**
     * Constructor.
     */
    public function __construct(private WpCreateNonce&WpVerifyNonce $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getNonceField(?string $action = null, ?string $name = null): string
    {
        $name = $name ?? self::DEFAULT_NAME;
        return '<input type="hidden" name="' . $name . '" value="' . $this->getNonce($action) . '">';
    }

    /**
     * @inheritDoc
     */
    public function printNonceField(?string $action = null, ?string $name = null): void
    {
        echo $this->getNonceField($action, $name);
    }

    /**
     * @inheritDoc
     */
    public function verifyNonceField(?string $nonceField = null, ?string $action = null): bool
    {
        $nonceField = $_REQUEST[$nonceField] ?? $_REQUEST[self::DEFAULT_NAME] ?? '';
        $action     = $action ?? self::DEFAULT_ACTION;

        return $this->wpService->wpVerifyNonce($nonceField, $action) !== false;
    }

    /**
     * @inheritDoc
     */
    public function getNonce(?string $action = null): string
    {
        return $this->wpService->wpCreateNonce($action ?? self::DEFAULT_ACTION);
    }
}
