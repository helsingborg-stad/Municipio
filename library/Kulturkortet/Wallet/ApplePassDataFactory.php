<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use RuntimeException;

/** Builds Apple Wallet JSON data from an active Vitec ticket and a category pass template. */
class ApplePassDataFactory
{
    /**
     * @param AppleWalletConfig $config Apple pass identifiers.
     * @param ApplePassTemplateLoader $templateLoader Loads category pass templates.
     */
    public function __construct(
        private AppleWalletConfig $config,
        private ApplePassTemplateLoader $templateLoader,
    ) {}

    /**
     * Creates Apple Wallet payload data by filling the category template placeholders from the Vitec ticket.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param string $barcode Ticket barcode, used as the card number.
     * @param string $category Category slug selecting the pass template.
     * @return array Pass data.
     * @throws RuntimeException When the ticket has no usable expiration date.
     */
    public function create(array $ticket, string $barcode, string $category): array
    {
        $expirationDate = WalletDateFormatter::formatDateTime($ticket['validUntil'] ?? null);
        if ($expirationDate === null) {
            throw new RuntimeException('The ticket does not contain a valid expiration date.');
        }

        $placeholders = [
            '{{APPLE_PASS_TYPE_IDENTIFIER}}' => $this->config->getPassTypeIdentifier(),
            '{{APPLE_TEAM_ID}}' => $this->config->getTeamIdentifier(),
            '{{kortnummer}}' => $barcode,
            '{{fornamn}}' => $this->getTicketString($ticket, 'firstname'),
            '{{efternamn}}' => $this->getTicketString($ticket, 'lastname'),
            '{{giltigtTom_ISO8601}}' => $expirationDate,
        ];

        return $this->replacePlaceholders($this->templateLoader->loadPassDefinition($category), $placeholders);
    }

    /**
     * Returns a trimmed string ticket value, or an empty string when it is missing.
     *
     * @param array $ticket Active Vitec ticket data.
     * @param string $key Ticket key.
     * @return string The trimmed value.
     */
    private function getTicketString(array $ticket, string $key): string
    {
        return is_string($ticket[$key] ?? null) ? trim($ticket[$key]) : '';
    }

    /**
     * Replaces placeholders in every string value of a decoded template, leaving keys untouched.
     *
     * @param array $data Decoded template data.
     * @param array<string, string> $placeholders Placeholder replacements keyed by placeholder.
     * @return array Data with placeholders replaced.
     */
    private function replacePlaceholders(array $data, array $placeholders): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->replacePlaceholders($value, $placeholders);
            } elseif (is_string($value)) {
                $data[$key] = strtr($value, $placeholders);
            }
        }

        return $data;
    }
}
