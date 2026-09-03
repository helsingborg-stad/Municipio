<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\TestCase;

final class BehaviourTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    public function testDoesNotRegisterTheObsoleteDrawerScreenSizesField(): void
    {
        new Behaviour('municipio_customizer_section_menu');

        $registeredSettings = array_column(PanelsRegistry::getInstance()->getRegisteredFields(), 'settings');

        static::assertNotContains('drawer_screen_sizes', $registeredSettings);
    }
}
