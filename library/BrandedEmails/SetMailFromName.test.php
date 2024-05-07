<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\Config\GetMailFrom;
use Municipio\BrandedEmails\Config\GetMailFromName;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class SetMailFromNameTest extends TestCase
{
    /**
     * @testdox getMailFromName() returns the name from the config service if it is set
     */
    public function testGetMailFromName()
    {
        $setMailFrom = new SetMailFromName($this->getConfigService('Bar'), $this->getWpService());
        $this->assertEquals('Bar', $setMailFrom->getMailFromName('Foo'));
    }

    /**
     * @testdox getMailFromName() returns the the default email if the config service returns null
     */
    public function testGetMailFromReturnsDefault()
    {
        $setMailFrom = new SetMailFromName($this->getConfigService(), $this->getWpService());
        $this->assertEquals('Foo', $setMailFrom->getMailFromName('Foo'));
    }

    /**
     * @testdox addHooks() is adding the filter to the wp_mail_from_name hook with a valid callback
     */
    public function testAddHooks()
    {
        $wpService   = $this->getWpService();
        $setMailFrom = new SetMailFromName($this->getConfigService(), $wpService);

        $setMailFrom->addHooks();

        $this->assertEquals('wp_mail_from_name', $wpService->filtersAdded[0][0]);
        $this->assertTrue(method_exists($setMailFrom, $wpService->filtersAdded[0][1][1]));
    }

    private function getConfigService(?string $name = null): GetMailFromName
    {
        return new class ($name) implements GetMailFromName {
            public function __construct(private ?string $name)
            {
            }

            public function getMailFromName(): ?string
            {
                return $this->name;
            }
        };
    }

    private function getWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $filtersAdded = [];
            public function addFilter(
                string $tag,
                callable $functionToAdd,
                int $priority = 10,
                int $acceptedArgs = 1
            ): bool {
                $this->filtersAdded[] = [ $tag, $functionToAdd, $priority, $acceptedArgs];
                return true;
            }
        };
    }
}
