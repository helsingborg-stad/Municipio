<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;

class SearchPageConfigTest extends TestCase
{
    public function testIsDisabledByDefault(): void
    {
        $config = new SearchPageConfig(new FakeAcfService(['getField' => false]));

        static::assertFalse($config->isEnabled());
    }

    public function testIsEnabledFromSettings(): void
    {
        $config = new SearchPageConfig(new FakeAcfService(['getField' => true]));

        static::assertTrue($config->isEnabled());
    }
}