<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetTaxonomies;

class AppearanceConfigFactoryTest extends TestCase
{
    #[TestDox('create returns an instance of AppearanceConfigInterface')]
    public function testCreateReturnsAppearanceConfigInterface(): void
    {
        $factory = new AppearanceConfigFactory($this->createMock(GetTaxonomies::class));

        $result = $factory->create(['archiveProps' => (object)[]]);

        $this->assertInstanceOf(AppearanceConfigInterface::class, $result);
    }
}
