<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Admin;

use AcfService\Contracts\AddOptionsSubPage;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\_x;

class OptionsPageTest extends TestCase
{
    #[TestDox('registers the options page before Municipio imports ACF fields on init')]
    public function testRegistersBeforeAcfFieldImport(): void
    {
        $wpService = $this->createMockForIntersectionOfInterfaces([AddAction::class, _x::class]);
        $wpService->expects(static::once())
            ->method('addAction')
            ->with('init', static::isType('callable'), 5);

        $optionsPage = new OptionsPage($wpService, $this->createMock(AddOptionsSubPage::class));
        $optionsPage->addHooks();
    }
}
