<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DrawerTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    #[TestDox('Drawer registers the "You are here" badge switch, off by default')]
    public function testDrawerRegistersTheCurrentBadgeSwitch(): void
    {
        $field = $this->findFieldBySettings($this->registerDrawerFields(), 'drawer_current_badge');

        static::assertNotNull($field);
        static::assertSame('switch', $field['type']);
        static::assertFalse($field['default']);
        static::assertSame([['type' => 'controller']], $field['output']);
    }

    /**
     * Register the drawer fields and return all registered Customizer fields.
     *
     * @return array<int, array<string, mixed>>
     */
    private function registerDrawerFields(): array
    {
        $drawer = new Drawer(Drawer::SECTION_ID);
        static::assertInstanceOf(Drawer::class, $drawer);

        return PanelsRegistry::getInstance()->getRegisteredFields();
    }

    /**
     * Get a registered Customizer field by its setting identifier.
     *
     * @param array<int, array<string, mixed>> $fields Registered fields.
     * @param string $settings Setting identifier.
     *
     * @return array<string, mixed>|null
     */
    private function findFieldBySettings(array $fields, string $settings): ?array
    {
        foreach ($fields as $field) {
            if (($field['settings'] ?? null) === $settings) {
                return $field;
            }
        }

        return null;
    }
}
