<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\AppleWallet\ApplePassGenerator;
use RuntimeException;

/** Builds Kulturkortet Apple Wallet passes from active Vitec tickets. */
class TicketApplePassGenerator
{
    /** @var string Category whose template is used for Kulturkortet cards. */
    private const CATEGORY = 'ordinarie';

    /**
     * @param ApplePassPlaceholderFactory $placeholderFactory Maps tickets to template placeholders.
     * @param ApplePassGenerator $applePassGenerator Generates signed Apple passes.
     */
    public function __construct(
        private ApplePassPlaceholderFactory $placeholderFactory,
        private ApplePassGenerator $applePassGenerator,
    ) {}

    /**
     * Generates a signed Apple Wallet pass for a ticket.
     *
     * @param array $ticket Active Vitec ticket data.
     * @return string The complete .pkpass archive.
     * @throws RuntimeException When the ticket or pass generation is invalid.
     */
    public function generate(array $ticket): string
    {
        $barcode = $ticket['barcode'] ?? null;
        if (!is_string($barcode) || $barcode === '') {
            throw new RuntimeException('The ticket does not contain a barcode.');
        }

        return $this->applePassGenerator->generate(
            self::CATEGORY,
            $this->placeholderFactory->create($ticket, $barcode),
        );
    }
}
