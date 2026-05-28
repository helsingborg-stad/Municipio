<?php

namespace Municipio\Chat\PIIRedactor;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\Passthrough\PassthroughPIIRedactor;
use Municipio\Chat\PIIRedactor\Presidio\PresidioRedactor;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PIIRedactorFactoryTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(PIIRedactorFactory::class, new PIIRedactorFactory(new FakeWpService()));
    }

    #[TestDox('create() returns a PIIRedactorInterface')]
    public function testCreateReturnsPIIRedactorInterface(): void
    {
        $factory = new PIIRedactorFactory(new FakeWpService());

        $this->assertInstanceOf(PIIRedactorInterface::class, $factory->create($this->getConfig()));
    }

    #[TestDox('create() returns a PassthroughPIIRedactor when presidio is not enabled')]
    public function testCreateReturnsPassthroughPIIRedactorWhenPresidioDisabled(): void
    {
        $factory = new PIIRedactorFactory(new FakeWpService());

        $this->assertInstanceOf(
            PassthroughPIIRedactor::class,
            $factory->create($this->getConfig(['chat_presidio_enabled' => false])),
        );
    }

    #[TestDox('create() returns a PassthroughPIIRedactor when the chat_presidio_enabled field is unset')]
    public function testCreateReturnsPassthroughPIIRedactorWhenPresidioEnabledFieldUnset(): void
    {
        $factory = new PIIRedactorFactory(new FakeWpService());

        $this->assertInstanceOf(PassthroughPIIRedactor::class, $factory->create($this->getConfig()));
    }

    #[TestDox('create() returns a PresidioRedactor when presidio is enabled')]
    public function testCreateReturnsPresidioRedactorWhenPresidioEnabled(): void
    {
        $factory = new PIIRedactorFactory(new FakeWpService());

        $this->assertInstanceOf(
            PresidioRedactor::class,
            $factory->create($this->getConfig(['chat_presidio_enabled' => true])),
        );
    }

    private function getConfig(array $fields = []): ChatConfigInterface
    {
        $acfService = new FakeAcfService([
            'getField' => fn(string $selector) => $fields[$selector] ?? null,
        ]);

        return new ChatConfig(new FakeWpService(['determineLocale' => 'en_US']), $acfService);
    }
}
