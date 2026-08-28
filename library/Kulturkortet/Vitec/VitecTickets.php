<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

class VitecTickets
{
    /**
     * Try to get the active ticket from the list of tickets.
     * To be active,
     * - the ticket must have 'Import_Kulturkort' in its template name
     * - the current date must be within the validFrom and validUntil range
     *
     * @param array $tickets List of tickets to check.
     * @return array|null The active ticket if found, otherwise null.
     */
    public static function tryGetActiveTicket(array $tickets): ?array
    {
        $now = new \DateTime();
        return (
            array_values(
                array_filter(
                    $tickets ?? [],
                    fn($t) => str_contains($t['ticketTemplateName'], 'Import_Kulturkort') && !empty($t['validFrom'] ?? '') && !empty($t['validUntil'] ?? '') && new \DateTime($t['validFrom'] ?? '') <= $now && new \DateTime($t['validUntil'] ?? '') >= $now,
                ),
            )[0] ?? null
        );
    }
}
