<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

use DateTime;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class VitecTicketTest extends TestCase
{
    private function makeTestTicket(int $id, string $ticketTemplateName, int $validFromDays, int $validUntilDays): array
    {
        $validFrom = (new \DateTime())
            ->modify("{$validFromDays} days")
            ->format('c');
        $validUntil = (new \DateTime())
            ->modify("{$validUntilDays} days")
            ->format('c');

        return [
            'id' => $id,
            'ticketTemplateName' => $ticketTemplateName,
            'validFrom' => $validFrom,
            'validUntil' => $validUntil,
        ];
    }

    #[TestDox('ticketTemplateName must contain Import_Kulturkort')]
    public function testTicketTemplateNameContainsImportKulturkort(): void
    {
        $tickets = [
            $this->makeTestTicket(1, 'Evenemang', -10, 10),
            $this->makeTestTicket(2, 'Import_Kulturkort kanske med extra kampanj', -10, +10),
        ];

        $activeTicket = VitecTickets::tryGetActiveTicket($tickets);
        assertTrue(is_array($activeTicket));
        assertEquals($activeTicket['id'], 2);
        assertEquals($activeTicket['ticketTemplateName'], 'Import_Kulturkort kanske med extra kampanj');
    }

    #[TestDox('current date must be within the validFrom and validUntil range')]
    public function testCurrentDateWithinValidRange(): void
    {
        $tickets = [
            $this->makeTestTicket(1, 'Evenemang', -10, 10),
            $this->makeTestTicket(2, 'Import_Kulturkort utgången', -20, -5),
            $this->makeTestTicket(3, 'Import_Kulturkort giltig', -20, +20),
            $this->makeTestTicket(4, 'Import_Kulturkort giltig men redundant', -20, +20),
            $this->makeTestTicket(5, 'Import_Kulturkort framtida', +20, +40),
            $this->makeTestTicket(6, 'Import_Kulturkort utgången', -20, -5),
        ];

        $activeTicket = VitecTickets::tryGetActiveTicket($tickets);
        assertTrue(is_array($activeTicket));
        assertEquals($activeTicket['id'], 3);
    }
}
